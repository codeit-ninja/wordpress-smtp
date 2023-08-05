<div class="smtp-cli">
    <header class="smtp-cli-header">
        <button class="smtp-cli-red-btn"></button>
        <button class="smtp-cli-yellow-btn"></button>
        <button class="smtp-cli-green-btn"></button>
    </header>
    <div id="smtp-test-response"></div>
</div>
<style>
    .smtp-cli {
        background-color: #1d2327;
        margin-right: 20px;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .smtp-cli-header {
        display: flex;
        padding: 1.5rem 2rem;
        gap: .5rem;
    }

    .smtp-cli-header button {
        height: 15px;
        width: 15px;
        border-radius: 50%;
        border: none;
    }

    button.smtp-cli-red-btn {
        background-color: #FE5F56;
    }

    button.smtp-cli-yellow-btn {
        background-color: #FDBD2F;
    }

    button.smtp-cli-green-btn {
        background-color: #2BC843;
    }

    #smtp-test-response {
        display: none;
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        height: 450px;
        overflow: auto;
        padding: 1.25rem 1.75rem;
        font-size: 1rem;
        scrollbar-width: auto;
        scrollbar-color: white #1d2327;
    }

    #smtp-test-response::-webkit-scrollbar {
        width: 15px;
    }

    #smtp-test-response::-webkit-scrollbar-track {
        background: #1d2327;
    }

    #smtp-test-response::-webkit-scrollbar-thumb {
        background-color: #515b60;
        border-radius: 10px;
        border: 5px solid #1d2327;
    }

    #smtp-test-response div {
        display: flex;
        gap: .5rem;
        margin-bottom: .3rem;
    }

    #smtp-test-response div:last-child::after {
        background-color: white;
        content: " ";
        height: 20px;
        width: 1px;
        right: 0;
        animation: 1s steps(2, start) blink infinite;
    }

    .smtp-info {
        margin: 1rem 0 1rem !important;
        color: white;
    }

    .smtp-client {
        color: #2BC843;
    }

    .smtp-server {
        color: aqua;
    }

    .smtp-error {
        color: #FE5F56;
    }

    @keyframes blink {
        to { visibility: hidden; }
    }
</style>