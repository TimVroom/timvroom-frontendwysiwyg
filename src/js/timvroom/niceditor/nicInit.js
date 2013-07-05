(function () {
    "use strict";

    bkLib.onDomLoaded(function () {
        new editorClass();
    });
    var editorClass = Class.create({
        editor : null,
        panelOffset : 0,
        onScrollObserver : null,

        initialize : function () {
            this.url = window.location.pathname;
            this.onScrollObserver = this.checkPanelOffset.bindAsEventListener(this);
            this.onToggle = this.toggleEditor.bindAsEventListener(this);
            this.target = $($$('.col-main > .std')[0]);

            this.toggleButton = new Element('div', {id : 'cmsToggleButton'}).setStyle({
                'position' : 'fixed',
                'z-index': '10',
                'top' : 0,
                'left' : 0,
                'border' : '1px solid #000',
                'padding' : '6px',
                'background' : '#cccccc'
            }).update('Toggle Editor');

            this.attach();
        },
        attach : function () {
            $$('body')[0].insert({top: this.toggleButton});
            Event.observe(this.toggleButton, 'click', this.onToggle);
        },
        createPanel: function() {
            this.panel = new Element('div', {'id' : 'nicCmsPanel'}).setStyle({'width' : this.target.getWidth() + 'px;'});
            this.target.insert({before: this.panel});
            this.panelOffset = this.panel.cumulativeOffset()['top'];
            if(this.editor){
                this.editor.setPanel(this.panel);
            }
            this.onScrollObserver();
            Event.observe(window, 'scroll', this.onScrollObserver);
        },
        destroyPanel: function() {
            Event.stopObserving(window, 'scroll', this.onScrollObserver);
            if (this.editor){
                this.editor.removePanel(this.panel);
            }
        },
        checkPanelOffset : function () {
            if (window.scrollY > this.panelOffset) {
                this.panel.setStyle({'position' : 'fixed', 'top' : '0px', 'width' : this.panel.getWidth() + 'px'});
            } else {
                this.panel.setStyle({'position' : 'static', 'top' : 'auto', 'width' : this.panel.getWidth() + 'px'});
            }
        },
        loadEditor : function () {
            this.editor = new nicEditor({
                fullPanel : true,
                iconsPath : '/js/timvroom/niceditor/nicEditorIcons.gif',
                onSave: this.saveContent.bindAsEventListener(this)
            });
            this.createPanel();
            this.editor.addInstance(this.target);
            this.loadContent(true);
        },
        toggleEditor : function () {
            if (!this.editor) {
                this.loadEditor();
            } else {
                this.destroyPanel();
                this.editor.removeInstance(this.target);
                this.editor = null;
                this.loadContent(false);
            }
        },
        loadContent: function(editable){
            editable = editable || false;
            console.log(editable);
            this.target.addClassName('loading');
            new Ajax.Request('timvroom_wysiwyg/ajaxcms/get', {
                parameters: {
                    editable : editable,
                    url: this.url
                },
                onComplete: this.submitComplete.bindAsEventListener(this)
            });
        },
        submitComplete: function(response) {
            console.log(response);
            this.target.removeClassName('loading');
            this.target.update(response.responseText);
        },
        saveContent: function(content, id, instance) {
            this.target.addClassName('loading');
            new Ajax.Request('timvroom_wysiwyg/ajaxcms/save', {
                url: this.url,
                content: content,
                onComplete: this.submitContentComplete.bindAsEventListener(this)
            });
        },
        submitContentComplete : function(response) {
            this.target.removeClassName('loading');
        }
    });
})();
