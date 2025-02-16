const js = require('@eslint/js');

module.exports = {
    ...js.configs.all,
    languageOptions: {
        ecmaVersion: "latest",
        sourceType: "module",
    },
    rules: {
        indent: [
            "error",
            4,
        ],
        "no-var": "error",
        semi: [
            "error",
            "always",
        ],
    },
};
