document.addEventListener('DOMContentLoaded', () => {
    // const enableSMTPCheckbox = document.querySelector('[name="codeit-smtp[smtp-enable]"]');
    const testConnectionBtn = document.querySelector('[name="codeit-smtp[smtp-test]"]');
    const testResponse = document.querySelector('#smtp-test-response');
    // const disableInputs = [
    //     'smtp-host',
    //     'smtp-port',
    //     'smtp-security',
    //     'smtp-username',
    //     'smtp-password',
    //     'smtp-test'
    // ];
    // const disableInputsElements = disableInputs.map(name => document.querySelector(`[name="codeit-smtp[${name}]"]`)).filter(e => e);
    
    // enableSMTPCheckbox.addEventListener('change', (e) => {
    //     if( e.target.checked ) {
    //         disableInputsElements.forEach(el => el.disabled = false);
    //     } else {
    //         disableInputsElements.forEach(el => el.disabled = true);
    //     }
    // })

    testConnectionBtn.addEventListener('click', async () => {
        const form = document.querySelector('form');
        const formData = new FormData(form);

        // This will invalidate the request (403)
        formData.delete('_wpnonce');
        formData.delete('_wp_http_referer');


        testResponse.style.display = 'block';
        testResponse.innerHTML += '<div class="smtp-info">Sending a test email to SMTP server...</div>';
        testResponse.scrollTop = testResponse.scrollHeight;

        const request = await fetch('/wp-json/codeit-smtp/v1/test', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        });
        const response = await request.text();
        const lines = response.split('\n').filter(l => l);
        
        let delayInMs = Math.floor(Math.random() * 150) + 0;

        lines.forEach((line, index) => {
            const lineEl = document.createElement('div');
            
            line = line.replace('<br>', '');
            line = line.replace('-&gt;', '>>');
            line = line.replace(/^(-?\d{1,2})+\s(\d+:?){1,}\s/, '');

            lineEl.innerHTML = '<span style="color: white;">></span> ' + line;

            if( line.includes('SERVER >> CLIENT') ) {
                lineEl.classList.add('smtp-client');
            }

            if( line.includes('CLIENT >> SERVER') ) {
                lineEl.classList.add('smtp-server');
            }

            if( line.includes('ERROR') || line.includes('SMTP Error') || line.includes('failed') ) {
                lineEl.classList.add('smtp-error');
            }

            setTimeout( () => {
                testResponse.append(lineEl);
                testResponse.scrollTop = testResponse.scrollHeight;
            }, delayInMs);

            delayInMs = delayInMs + Math.floor(Math.random() * 150) + 0;
        })
    });
})