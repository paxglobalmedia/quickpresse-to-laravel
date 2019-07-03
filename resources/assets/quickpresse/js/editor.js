/**
 * Created by jclora on 2015-10-01.
 */


var textEditor = function () {

    /*
     * Initialize Redactor editor
     */
    var redactorOptions = {
        //imageEditable: true,
        //imageResizable: true,
        source: true,
        //removeDataAttr: false,
        minHeight: 200,
        maxHeight: 250,
        //lang: 'fr',
        initCallback: function()
        {
            //console.log(this);
        }
    }

    redactorOptions.buttons = ['html', 'formatting', 'bold', 'italic',
        'unorderedlist', 'orderedlist',
        'link', 'alignment', 'horizontalrule'];

    $('.text-editor').redactor(redactorOptions);


}

textEditor();