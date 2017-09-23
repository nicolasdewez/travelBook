let $ = require('jquery');
let SearchPlace = require('./travels');

$(function() {
    let searchPlaceInstance = new SearchPlace();

    searchPlaceInstance.init(
        $('#picture_placeSearch'),
        $('#user_lang'),
        $('#picture_place'),
        $('input[type=submit]')
    );
});
