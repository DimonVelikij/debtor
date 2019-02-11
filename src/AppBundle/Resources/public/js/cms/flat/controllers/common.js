(function (angular) {
    "use strict";

    angular
        .module('cms.flat')
        .controller('CommonCtrl', CommonCtrl);

    CommonCtrl.$inject = [
        '$scope'
    ];

    function CommonCtrl(
        $scope
    ) {
        /**
         *
         * @type {{flat: {}}}
         */
        $scope.forms = {
            flat: {}
        };

        /**
         *
         * @type {{flatTypes: Array, houses: Array, flat: {id: null, number: null, archive: boolean, house: {}, type: {}}}}
         */
        $scope.state = {
            loading: true,//текущая загрузка
            flatTypes: [],//типы помещений
            houses: [],//дома
            flat: {//объект помещения
                id: null,
                number: null,
                archive: false,
                house: null,
                type: null
            },
            flatUpdated: false//помещение обновлено
        };
    }
})(angular);
