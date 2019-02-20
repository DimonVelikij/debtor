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
         * @type {{flatTypes: Array, houses: Array, flat: {id: null, number: null, archive: boolean, house: {}, type: {}}}}
         */
        $scope.state = {
            loading: true,//текущая загрузка
            blocks: {//блоки
                flat: {//помещение
                    types: [],//типы помещения
                    houses: [],//дома
                    model: {//объект помещения
                        id: null,
                        number: null,
                        archive: false,
                        house: null,
                        type: null
                    },
                    isUpdated: false//помещение обновлено
                },
                personalAccount: {//лицевые счета
                    toggle: false,
                    class: 'fa-plus-square'
                }
            }
        };

        /**
         * открытие закрытие блока
         * @param blockName
         */
        $scope.toggleBlock = function (blockName) {
            $scope.state.blocks[blockName].toggle = !$scope.state.blocks[blockName].toggle;
            $scope.state.blocks[blockName].class = $scope.state.blocks[blockName].toggle ? 'fa-minus-square' : 'fa-plus-square';
        }
    }
})(angular);
