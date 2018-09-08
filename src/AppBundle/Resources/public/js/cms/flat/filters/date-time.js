(function () {
    "use strict";

    angular
        .module('cms.flat')
        .filter('dateTime', DateTimeFilter);

    DateTimeFilter.$inject = [
        '$filter'
    ];

    function DateTimeFilter (
        $filter
    ) {
        return function (dateTime) {
            if (!dateTime) {
                return;
            }

            if (dateTime.length !== 12) {
                throw new Error('String length must be 12 numbers');
            }

            var date = $filter('date')(dateTime.substr(0,8));

            return date + ' ' + dateTime.substr(8, 2) + ':' + dateTime.substr(10, 2);
        }
    }

})(angular);