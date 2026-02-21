window.onload = function() {
    const swaggerUI = document.getElementById('swagger-ui');
    if (!swaggerUI) {
        return;
    }

    const specUrl = swaggerUI.dataset.specUrl;
    if (!specUrl) {
        console.error('Swagger UI specification URL is not provided.');
        return;
    }

    window.ui = SwaggerUIBundle({
        url: specUrl,
        domNode: swaggerUI,
        deepLinking: true,
        presets: [
            SwaggerUIBundle.presets.apis,
        ],
        plugins: [
            SwaggerUIBundle.plugins.DownloadUrl
        ],
        layout: "BaseLayout",
        showCommonExtensions: true,
        tryItOutEnabled: true,
    });
};
