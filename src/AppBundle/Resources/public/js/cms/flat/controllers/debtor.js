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
            name: null,//фио или наименование организации
            phone: null,//телефон
            email: null,//email
            location: null,//место нахождения или жительства
            //физ лицо
            dateOfBirth: null,//дата рождения физ лица
            placeOfBirth: null,//место рождения физ лица
            //индивидуальный предприниматель
            ogrnip: null,//ОГРНИП
            inn: null,//ИНН
            //юр лицо
            ogrn: null,//ОГРН
            bossName: null,//ФИО руководителя
            bossPosition: null,//должность руководителя
            //статус собственности
            ownershipStatus: null,//статус собстенности
            ownershipSubStatus: null,//доп статус
            shareSize: null,//размер доли
            ownerName: null,//фио собственника
            startDateOwnership: null,//дата начала собственности
            endDateOwnership: null//дата окончания собственности
        };

        /**
         * отслеживаем изменение типа должника
         */
        $scope.$watch('currentDebtor.type', function (newType) {
            if (!newType) {
                return;
            }

            resetOwnershipSubStatuses();
        });

        /**
         * отслеживаем изменение статуса собственности
         */
        $scope.$watch('currentDebtor.ownershipStatus', function (newStatus) {
            if (!newStatus) {
                return;
            }

            //обнуляем доп статусы или заменяем
            $scope.ownershipSubStatuses = newStatus.children ? newStatus.children : [];

            resetOwnershipSubStatus();
        });

        /**
         * отслеживаем изменение доп статуса собстенности
         */
        $scope.$watch('currentDebtor.ownershipSubStatus', function (newStatus) {
            if (!newStatus) {
                return;
            }

            resetAdditionalOwnershipStatus();
        });

        /**
         * сброс списка доп статусов
         */
        function resetOwnershipSubStatuses() {
            //сбрасываем список доп статусов
            $scope.ownershipSubStatuses = [];
            resetOwnershipSubStatus();
        }

        /**
         * сброс доп статуса и доп параметров
         */
        function resetOwnershipSubStatus() {
            //сбрасываем доп статус из модели
            $scope.currentDebtor.ownershipSubStatus = null;
            resetAdditionalOwnershipStatus();
        }

        /**
         * сброс доп параметров статусов собственности
         */
        function resetAdditionalOwnershipStatus() {
            //сбрасываем размер доли
            $scope.currentDebtor.shareSize = null;
            //сбрасываем фио собственника
            $scope.currentDebtor.ownerName = null;
        }

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