CMS.Use([], function (CMS) {
    CMS.Block = Class.extend({

        id: null,
        weight: null,
        actions: [],
        location: null,
        title: null,

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
                pluginPaths.push('Core/CMS.BlockAction.'+this.actions[i].plugin);
            }

            CMS.Use(pluginPaths, function () {
                if (!self.domElement.children().size()) {
                    self.domElement.append($.tmpl('<div class="edit-node-label"><h6>${title}</h6></div>', {
                        'title' : self.title
                    }));
                }
                var actionsBlock = $('<ul>', {
                    css: {
                        position: 'absolute',
                        right: 0,
                        top: 0
                    },
                    'class': 'block-actions'
                });
                self.domElement.parent().css('position', 'relative').prepend(actionsBlock);
                for (i in self.actions) {
                    self.actions[i].blockId = self.id;
                    self.actions[i] = CMS.BlockAction.Action.createAction(self.actions[i]);
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