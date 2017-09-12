function PlaceForm(searchField, searchButton, submitButton, choiceBlock, localeField, latitudeField, longitudeField)
{
    this.searchButton = searchButton;
    this.searchField = searchField;
    this.submitButton = submitButton;
    this.choiceBlock = choiceBlock;
    this.localeField = localeField;
    this.latitudeField = latitudeField;
    this.longitudeField = longitudeField;

    this.onClickSearchButton = function(e) {
        e.preventDefault();

        this.choiceBlock.html('');
        this.disableSubmitButton();

        let url = Routing.generate('api_places_search_in_web', {
            query: this.searchField.val(),
            locale: this.localeField.val()
        }, true);

        $.get(url, function(data){
            this.choiceBlock.append('<div id="choice_place_results">')

            data.forEach(function(result) {
                let value = result.title + '#' + result.locale + '#' + result.latitude + '#' + result.longitude;

                this.choiceBlock.append(
                    '<div class="radio">' +
                    '<label>' +
                    '<input name="place_choice" value="' + value + '" type="radio">' +
                    result.title + ' (' + result.locale + ') ' + result.linkShow +
                    '</label>' +
                    '</div>'
                );

                this.choiceBlock.find('input[type=radio][name=place_choice]').click(this.onClickChoiceButton.bind(this));
            }.bind(this));

            this.choiceBlock.append('</div>')
        }.bind(this));
    };

    this.onClickChoiceButton = function(e) {
        let element = $(e.currentTarget);
        let values = element.val().split('#');
        this.searchField.val(values[0]);
        this.localeField.val(values[1]);
        this.latitudeField.val(values[2]);
        this.longitudeField.val(values[3]);
        this.enableSubmitButton();
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
        this.searchButton.click(this.onClickSearchButton.bind(this));
    };

    this.listenEvents();
}

$(function() {
    new PlaceForm(
        $('#place_title'),
        $('.button-search-place'),
        $('input[type=submit]'),
        $('#choice_place'),
        $('#place_locale'),
        $('#place_latitude'),
        $('#place_longitude')
    );
});
