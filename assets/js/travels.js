require('easy-autocomplete/dist/jquery.easy-autocomplete');

function SearchPlace()
{
    this.searchField = null;
    this.localeField = null;
    this.placeField = null;
    this.submitButton = null;

    this.init = function(searchField, localeField, placeField, submitButton) {
        this.searchField = searchField;
        this.localeField = localeField;
        this.placeField = placeField;
        this.submitButton = submitButton;

        this.listenEvents();
    };

    this.onSelectPlace = function() {
        if (this.placeField.val() !== '') {
            this.enableSubmitButton();
            return;
        }

        this.disableSubmitButton();
    };

    this.enableSubmitButton = function() {
        this.submitButton.removeClass('disabled');
        this.submitButton.removeAttr('disabled')
    };

    this.disableSubmitButton = function() {
        this.submitButton.addClass('disabled');
        this.submitButton.attr('disabled', true);
    };

    this.listenEvents = function() {
        // Auto complete places
        this.searchField.easyAutocomplete({
            url: function(query) {
                return Routing.generate('api_places_get_collection', {
                    title: query,
                    locale: this.localeField.val()
                }, true);
            }.bind(this),

            getValue: 'title',
            theme: 'bootstrap',

            list: {
                onChooseEvent: function() {
                    let item = this.searchField.getSelectedItemData();
                    this.placeField.val(item.id).trigger('change');
                }.bind(this),
                onShowListEvent: function() {
                    this.placeField.val('').trigger('change');
                }.bind(this)
            }
        });

        // Enable submit button if place choice
        this.placeField.change(this.onSelectPlace.bind(this));
    };
}


module.exports = SearchPlace;
