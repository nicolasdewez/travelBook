let $ = require('jquery');
let SearchPlace = require('./travels');

$(function() {
    let searchPlaceInstance = new SearchPlace();

    searchPlaceInstance.init(
        $('#travel_placeSearch'),
        $('#user_lang'),
        $('#travel_place'),
        $('input[type=submit]')
    );
});
