(function () {
    "use strict";

    angular
        .module('cms.flat')
        .filter('date', DateFilter);

    DateFilter.$inject = [];

    function DateFilter () {
        return function (date) {
            if (!date) {
                return;
            }

            if (date.length !== 8) {
                throw new Error('String length must be 8 numbers');
            }

            return date.substr(0, 2) + '.' + date.substr(2, 2) + '.' + date.substr(4, 4);
        }
    }

})(angular);