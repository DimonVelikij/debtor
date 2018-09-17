CKEDITOR.plugins.addExternal('lineheight', '/bundles/app/js/cms/ckeditor/lineheight/plugin.js');

CKEDITOR.editorConfig = function (config) {
    config.extraPlugins = 'lineheight';
};
