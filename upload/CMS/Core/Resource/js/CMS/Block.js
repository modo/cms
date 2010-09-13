CMS.Use([], function (CMS) {
    CMS.Block = Class.extend({

        id: null,
        weight: null,
        actions: [],

        domElement: null,

        init: function (data) {
            $.extend(this, data);
            this.domElement = $('#block-' + this.id);
            this._setupActions();
        },

        _setupActions: function () {
            var self = this;

            var pluginPaths = [];
            for (i in this.actions) {
                pluginPaths.push('Core/CMS.Action.'+this.actions[i].plugin);
            }

            CMS.Use(pluginPaths, function () {
                var actionsBlock = $('<ul class="block-actions">');
                self.domElement.parent().prepend(actionsBlock);
                for (i in self.actions) {
                        self.domElement.trigger('Action loaded');
                        self.actions[i] = CMS.Action.Action.createAction(self.actions[i]);
                        actionsBlock.prepend(self.actions[i].domElement);
                }
            });
            
        },

        getPosition: function () {
            return this.domElement.parents('.block-wrapper:first').index();
        },

        getLayout: function () {
            return this.domElement.parents('.location:first').attr('id');
        }
    });
});