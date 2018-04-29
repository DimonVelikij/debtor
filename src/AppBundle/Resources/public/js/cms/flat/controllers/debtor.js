(function (angular) {
    "use strict";

    angular
        .module('cms.flat')
        .controller('DebtorCtrl', DebtorCtrl);

    DebtorCtrl.$inject = [
        '$scope',
        '$http',
        '$q',
        'Initializer',
        'FormHelper'
    ];

    function DebtorCtrl(
        $scope,
        $http,
        $q,
        Initializer,
        FormHelper
    ) {
        /**
         * форма
         * @type {{}}
         */
        $scope.debtor = {};

        /**
         * загрузка списка должников и типов должников
         */
        $q.all([
            $http.get(Initializer.Path.AdminDebtorList),
            $http.get(Initializer.Path.AdminDebtorTypes)
        ]).then(function (response) {
            $scope.debtors = response[0].data;
            $scope.debtorTypes = response[1].data;
        });

        /**
         * модели для полей формы
         * @type {{type: null}}
         */
        $scope.currentDebtor = {
            type: null,//тип должника
            ownershipStatus: null,//статус собстенности
            ownershipSubStatus: null,//доп статус
            shareSize: null//размер доли
        };

        /**
         * отслеживаем изменение статуса собственности
         */
        $scope.$watch('currentDebtor.ownershipStatus', function (newStatus) {
            if (!newStatus) {
                return;
            }

            //обнуляем доп статусы или заменяем
            $scope.ownershipSubStatuses = newStatus.children ? newStatus.children : [];
            //сбрасываем доп статус из модели
            $scope.currentDebtor.ownershipSubStatus = null;
            //сбрасываем размер доли
            $scope.currentDebtor.shareSize = null;
            //также сбрасывать все параметры связаные со статусом собственности
        });

        /**
         * отслеживаем изменение доп статуса собстенности
         */
        $scope.$watch('currentDebtor.ownershipSubStatus', function (newStatus) {
            if (!newStatus) {
                return;
            }

            //сбрасываем размер доли
            $scope.currentDebtor.shareSize = null;
        });

        $scope.submit = function ($event, form) {
            $event.preventDefault();

            FormHelper.forceDirty(form);

            if (form.$invalid) {
                return;
            }

            console.log($scope.currentDebtor);
        };
    }

})(angular);