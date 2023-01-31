(function ($) {
    if (typeof acf === "undefined" || typeof acfe === "undefined") {
        return;
    }

    /**
     * Field: TM Link
     */
    var TmLink = acf.Field.extend({
        type: "TmLink",

        events: {
            'click a[data-name="add"]': "onClickEdit",
            'click a[data-name="edit"]': "onClickEdit",
            'click a[data-name="remove"]': "onClickRemove",
        },

        $control: function () {
            return this.$(".acf-link");
        },

        getSubField: function (key) {
            return acf
                .getFields({
                    key: key,
                    parent: this.$el,
                })
                .shift();
        },

        getSubFields: function () {
            return acf.getFields({
                parent: this.$el,
            });
        },

        getValue: function () {
            var target = this.getSubField("target");
            if (target) {
                target = target.val();
            }
            var label = this.getSubField("label");
            if (label) {
                label = label.val();
            }
            // return
            var data = {
                type: this.getSubField("type").val(),
                title: label,
                value: "",
                name: "",
                target: target,
            };

            // assign value
            data.value = this.getSubField(data.type).val();
            data.name = data.value;

            // post value
            if (data.type === "post") {
                data.name = this.getSubField(data.type)
                    .$input()
                    .find(":selected")
                    .text();
            }

            // return
            return data;
        },

        setValue: function (val) {
            // clear value
            if (!val) {
                return this.clearValue();
            }

            // allow val to be a string
            if (acfe.isString(val)) {
                val = {
                    type: "url",
                    title: "",
                    value: val,
                    target: false,
                };
            }

            val = acf.parseArgs(val, {
                type: "url",
                value: "",
                title: "",
                target: false,
            });

            // set sub fields
            this.getSubField("type").val(val.type);
            this.getSubField(val.type).val(val.value); // post value
            this.getSubField("title").val(val.title);
            this.getSubField("target").val(val.target);

            // render value
            this.renderValue();
        },

        clearValue: function () {
            // clear subfields values
            this.getSubFields().map(function (field) {
                field.val("");
                if (field.select2) {
                    // clear select2 value
                    field.select2.$el.val(null).trigger("change");
                }
            });
        },

        renderValue: function () {
            // vars
            var val = this.val();
            var $control = this.$control();

            // remove class
            $control.removeClass("-value -external");

            // add class
            if (val.value || val.title) {
                $control.addClass("-value");
            }

            // target
            if (val.target) {
                $control.addClass("-external");
            }

            // update text
            var url = val.type === "url" ? val.value : "#";
            var title = val.title ? val.title : val.name;
            this.$(".link-title").html(title);
            this.$(".link-url").attr("href", url).html(val.name);
        },

        onClickEdit: function (e, $el) {
            this.getModal({
                open: true,
                onClose: this.proxy(function () {
                    this.renderValue();
                }),
            });
        },

        onClickRemove: function (e, $el) {
            this.clearValue();
            this.renderValue();
        },
    });

    acf.registerFieldType(TmLink);

    /**
     * Field: Advanced Link Ajax Manager
     */
    new acf.Model({
        filters: {
            "select2_ajax_data/action=tm/fields/tm_link/post_query": "ajaxData",
        },

        ajaxData: function (ajaxData, data, $el, field, select) {
            // get advanced link field
            var parentField = field.parent();

            // assign parent field key
            if (parentField) {
                const linkField = acf.getFields({
                    key: "post_types",
                    parent: parentField.$el,
                })[0];
                if (linkField && linkField.val()) {
                    ajaxData["post_types"] = linkField.val();
                }

                ajaxData.field_key = parentField.get("key");
            }

            return ajaxData;
        },
    });
})(jQuery);
