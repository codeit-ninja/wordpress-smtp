<?php
namespace CodeIT\SMTP;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Options
{
    private string $slug = 'codeit_smtp_options';

    private array $options;

    function __construct()
    {
        $this->options = get_option('codeit-smtp') ? get_option('codeit-smtp') : [];
        
        add_action( 'admin_init', array( $this, 'settings_fields' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'rest_api_init', array( $this, 'register_rest_test_route' ) );
    }

    function admin_menu()
    {
        add_options_page(
            'SMTP Settings', 
            'SMTP', 
            'activate_plugins', 
            CODEIT_SMTP_PLUGIN_SLUG,
            array( $this, 'settings_page' )
        );
    }

    function settings_page()
    {
        ?>
        <div class="wrap">
			<h1><?php echo get_admin_page_title() ?></h1>
			<form method="post" action="options.php">
				<?php
					settings_fields( $this->slug );
					do_settings_sections( CODEIT_SMTP_PLUGIN_SLUG );
				?>
                <p class="submit">
                    <?php
                    submit_button('Save', 'primary', 'submit', false);
                    ?>
                    <button type="button" class="button button-secondary" name="codeit-smtp[smtp-test]" style="margin-left: 1rem;">Send a test email</button>
                </p>
			</form>
		</div>
        <?php
        include CODEIT_SMTP_ROOT_DIR_PATH . '/admin/smtp-test.php';
    }

    function settings_fields()
    {
        add_settings_section(
            'codeit_smtp_settings_section', // section ID
            '', // title (optional)
            '', // callback function to display the section (optional)
            CODEIT_SMTP_PLUGIN_SLUG
        );

        register_setting( $this->slug, 'codeit-smtp', array(
            'description' => 'Enable SMTP functionality?'
        ) );

        add_settings_field(
            'smtp-enable',
            'Enable SMTP?', 
            array( $this, 'render_enable_smtp_checkbox' ), 
            CODEIT_SMTP_PLUGIN_SLUG, 
            'codeit_smtp_settings_section',
        );

        add_settings_field(
            'smtp-host', 
            'SMTP server', 
            array( $this, 'render_smtp_host_field' ), 
            CODEIT_SMTP_PLUGIN_SLUG, 
            'codeit_smtp_settings_section',
            array(
                'label_for' => 'smtp-host',
                'name'      => 'smtp-host'
            )
        );

        add_settings_field(
            'smtp-port', 
            'Port', 
            array( $this, 'render_smtp_port_field' ), 
            CODEIT_SMTP_PLUGIN_SLUG, 
            'codeit_smtp_settings_section',
            array(
                'label_for' => 'smtp-port',
                'name'      => 'smtp-port'
            )
        );

        add_settings_field(
            'smtp-security', 
            'Security', 
            array( $this, 'render_smtp_security_field' ), 
            CODEIT_SMTP_PLUGIN_SLUG, 
            'codeit_smtp_settings_section',
            array(
                'label_for' => 'smtp-security',
                'name'      => 'smtp-security'
            )
        );

        add_settings_field(
            'smtp-username', 
            'Username', 
            array( $this, 'render_smtp_username_field' ), 
            CODEIT_SMTP_PLUGIN_SLUG, 
            'codeit_smtp_settings_section',
            array(
                'label_for' => 'smtp-username',
                'name'      => 'smtp-username'
            )
        );

        add_settings_field(
            'smtp-password', 
            'Password', 
            array( $this, 'render_smtp_password_field' ), 
            CODEIT_SMTP_PLUGIN_SLUG, 
            'codeit_smtp_settings_section',
            array(
                'label_for' => 'smtp-password',
                'name'      => 'smtp-password'
            )
        );
    }

    function render_enable_smtp_checkbox()
    {
        ?>
            <label>
                <input type="checkbox" name="codeit-smtp[smtp-enable]" <?php checked( $this->options['smtp-enable'], 'on' ) ?> /> Yes
            </label>
        <?php
    }

    function render_smtp_host_field( array $args )
    {
        printf(
            '<input type="text" id="%s" name="codeit-smtp[%s]" value="%s" class="regular-text" placeholder="IP, hostname etc" />',
            $args[ 'name' ],
            $args[ 'name' ],
            $this->options[$args[ 'name' ]]
        );
    }

    function render_smtp_port_field( array $args )
    {
        printf(
            '<input type="number" id="%s" name="codeit-smtp[%s]" value="%s" class="small-text" placeholder="587" />',
            $args[ 'name' ],
            $args[ 'name' ],
            $this->options[$args[ 'name' ]],
        );
    }

    public function render_smtp_security_field() {
        $items = [
            'None',
            'SSL',
            'TLS'
        ];

        ?> 
        <select name="codeit-smtp[smtp-security]" id="smtp-security">
            <?php 
            foreach( $items as $item ) {
                $selected = $this->options['smtp-security'] === $item ? 'selected' : '';
                echo "<option value='$item' $selected>$item</option>";
            }
            ?>
        </select>

        <?php
        echo '<label class="disabled">&nbsp;(It is recommended that the SMTP server uses SSL/TLS)</label>';
    }

    function render_smtp_username_field( array $args )
    {
        printf(
            '<input type="text" id="%s" name="codeit-smtp[%s]" value="%s" class="regular-text" placeholder="Username" />',
            $args[ 'name' ],
            $args[ 'name' ],
            $this->options[$args[ 'name' ]]
        );
    }

    function render_smtp_password_field( array $args )
    {
        printf(
            '<input type="text" id="%s" name="codeit-smtp[%s]" value="%s" class="regular-text" placeholder="Password" />',
            $args[ 'name' ],
            $args[ 'name' ],
            $this->options[$args[ 'name' ]]
        );
    }

    function register_rest_test_route()
    {
        register_rest_route( 'codeit-smtp/v1', '/test', array(
            'methods' => 'POST',
            'callback' => array( $this, 'test_smtp_connection' ),
        ) );
    }

    function test_smtp_connection( \WP_REST_Request $request )
    {
        $options = $request->get_body_params()['codeit-smtp'];
        $response = new \WP_REST_Response();
        $mail = new PHPMailer();

        $response->set_headers( array(
            'content-type' => 'text/plain'
        ) );
        
        try 
        {
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->Timeout = 15;
            $mail->isSMTP();

            if( $options['smtp-security'] === 'SSL' ) 
            {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            }
            elseif( $options['smtp-security'] === 'TLS' )
            {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
            else
            {
                $mail->SMTPSecure = '';
            }
            
            $mail->Host     = $options['smtp-host'];
            $mail->Port     = $options['smtp-port'];
            $mail->SMTPAuth = true;
            $mail->Username = $options['smtp-username'];
            $mail->Password = $options['smtp-password'];

            $mail->setFrom('no-reply@codeit.ninja', 'Code IT');
            $mail->addAddress(get_option('admin_email'), 'Code IT');

            $mail->isHTML(true);
            $mail->Subject = 'Code IT - SMTP Test';
            $mail->Body = '<b>SMTP Test message</b>';

            $mail->send();
        } 
        catch( Exception $e ) 
        {
            return $mail->ErrorInfo;
        }

        return $response;
    }
}