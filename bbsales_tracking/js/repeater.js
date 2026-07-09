jQuery.fn.extend({
    createRepeater: function (options = {}) {
        var hasOption = function (optionKey) {
            return options.hasOwnProperty(optionKey);
        };

        var option = function (optionKey) {
            return options[optionKey];
        };

        var generateId = function (string) {
            return string
                .replace(/\[/g, '_')
                .replace(/\]/g, '')
                .toLowerCase();
        };

        var addItem = function (items, key, fresh = true, stopdelete = 0) {
            var itemContent = items;
            var group = itemContent.data("group");
            var item = itemContent;
            var input = item.find('input,select');

            input.each(function (index, el) {
                var attrName = $(el).data('name');
                var attrVal = $(el).val();
                var skipName = $(el).data('skip-name');
                if (skipName != true) {
                    $(el).attr("name", group + "[" + key + "]" + "[" + attrName + "]");
                    $(el).attr("data-key", key);
                } else {
                    if (attrName != 'undefined') {
                        $(el).attr("name", attrName);
                    }
                }
                if (fresh == true && !$(el).data('value-copy')) {
                    $(el).attr('value', '');
                }
                else
                {
                    $(el).attr("value", attrVal);
                }

                $(el).attr('id', generateId($(el).attr('name')));
                $(el).parent().find('label').attr('for', generateId($(el).attr('name')));
            })

            var itemClone = items;

            /* Handling remove btn */
            var removeButton = itemClone.find('.remove-btn');

            if (key == stopdelete) {
                removeButton.attr('disabled', true);
            } else {
                removeButton.attr('disabled', false);
            }

            removeButton.attr('onclick', '$(this).parents(\'.divTableRow\').remove()');

            var newItem = $("<div class='items'>" + itemClone.html() + "<div/>");
            newItem.attr('data-index', key)

            // newItem.appendTo(repeater);
            functionAfterAdd(itemClone.html(),key,fresh);
        };

        /* find elements */
        var repeater = this;
        var items = repeater.find(".items");
        var key = 0;
        var addButton = repeater.find('.repeater-add-btn');

            
        items.each(function (index, item) {
            items.remove();
            if (hasOption('showFirstItemToDefault') && option('showFirstItemToDefault') == true) {
                if (hasOption('showFirstItemDefaultCount') && option('showFirstItemDefaultCount') > 0 && option('PageMode')!="edit")
                {
                    for (var dd = 0; dd < option('showFirstItemDefaultCount'); dd++) {
                        addItem($(item), key, false, key);
                        key++;
                    }
                }
                else
                {
                    addItem($(item), key, false, key);
                    key++;
                }
            } else {
                if (items.length > 1) {
                    addItem($(item), key);
                    key++;
                }
            }


        });

        /* handle click and add items */
        addButton.on("click", function () {
            addItem($(items[0]), key);
            key++;
        });
    }
});
