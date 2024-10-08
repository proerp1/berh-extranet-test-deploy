(function() {
    // Criar o botão do widget
    var widgetButton = document.createElement('div');
    widgetButton.className = 'redirect-widget';
    // widgetButton.style.backgroundImage = 'url("FINAISSimboloBE_Logo_Simbolo.png")';
    widgetButton.onclick = expandWidget;
    document.body.appendChild(widgetButton);

    // Criar o container do iframe
    var iframeContainer = document.createElement('div');
    iframeContainer.className = 'iframe-container';
    iframeContainer.id = 'iframeContainer';
    document.body.appendChild(iframeContainer);

    // Criar o botão de fechar
    var closeButton = document.createElement('span');
    closeButton.className = 'close-btn';
    closeButton.innerHTML = '&times;';
    closeButton.onclick = closeWidget;
    iframeContainer.appendChild(closeButton);

    // Criar o iframe    
    var iframe = document.createElement('iframe');
    iframe.src = 'https://contactmail.directtalk.com.br/clientes/BeRH/index.html?Nome='+v_user_name+'&Email='+v_user_email;
    iframe.id = 'iframeContainerBerh';
    iframe.title = 'Página de Suporte';
    iframeContainer.appendChild(iframe);

    // Função para expandir o widget
    function expandWidget() {
        iframeContainer.style.display = 'block';
    }

    // Função para fechar o widget
    function closeWidget() {
        iframeContainer.style.display = 'none';
    }
})();
