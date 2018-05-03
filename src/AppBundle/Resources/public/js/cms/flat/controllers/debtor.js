(function (angular) {
    "use strict";

    angular
        .module('cms.flat')
        .controller('DebtorCtrl', DebtorCtrl);

    DebtorCtrl.$inject = [
        '$scope',
        '$http',
        '$q',
        '$filter',
        '_',
        'Initializer',
        'FormHelper'
    ];

    function DebtorCtrl(
        $scope,
        $http,
        $q,
        $filter,
        _,
        Initializer,
        FormHelper
    ) {
        /**
         * форма
         * @type {{}}
         */
        $scope.debtor = {};

        /**
         * состояние формы
         * @type {{
             * openedDebtorForm: boolean, открыта ли форма создания или редактирования должника
             * currentDebtorType: null, текущий тип должника
             * currentDebtor: null редактируемый должник
             * }}
         */
        $scope.state = {
            openedDebtorForm: false,
            currentDebtorType: null,
            currentDebtor: {}
        };

        /**
         * открытие формы создания|редактирования должника
         * @param debtor
         */
        $scope.openDebtorForm = function (debtor) {
            $scope.state = {
                openedDebtorForm: true,
                currentDebtor: debtor ? debtor : {}
            };

            $scope.state.currentDebtorType = debtor ? debtor.type : null;
        };

        /**
         * закрытие формы создания|редактирования должника
         */
        $scope.closeDebtorForm = function () {
            $scope.state = {
                openedDebtorForm: false,
                currentDebtorType: null,
                currentDebtor: {}
            };
        };

        /**
         * загрузка списка должников и типов должников
         */
        $q.all([
            $http.get(Initializer.Path.AdminDebtorList),
            $http.get(Initializer.Path.AdminDebtorTypes),
            $http.get(Initializer.Path.AdminDebtorOwnershipStatuses)
        ]).then(function (response) {
            //список должников
            $scope.debtors = response[0].data;
            //типы должников
            $scope.debtorTypes = response[1].data;

            //статусы собственности
            $scope.ownershipStatuses = {
                individual: response[2].data,
                businessman: _.find(response[2].data, function (status) {
                    return status.alias === 'owner';
                }).children,
                legal: _.find(response[2].data, function (status) {
                    return status.alias === 'owner';
                }).children
            };
        });

        $scope.$watch('state.currentDebtorType', function (newDebtorType) {
            if (!newDebtorType) {
                return;
            }

            var treeStatuses = $scope.state.currentDebtor.ownershipStatus ?
                getTreeOwnershipStatuses($scope.ownershipStatuses[newDebtorType.alias], $scope.state.currentDebtor.ownershipStatus.alias, []) :
                [];

            $scope.currentDebtor = {
                name: $scope.state.currentDebtor.name,
                phone: $scope.state.currentDebtor.phone ? parseInt($scope.state.currentDebtor.phone) : null,
                email: $scope.state.currentDebtor.email,
                location: $scope.state.currentDebtor.location,
                archive: $scope.state.currentDebtor.archive,
                subscriber: $scope.state.currentDebtor.subscriber,
                // ownershipStatus: $scope.state.currentDebtor.ownershipStatus.parent ? $scope.state.currentDebtor.ownershipStatus.parent : $scope.state.currentDebtor.ownershipStatus,
                // ownershipSubStatus: $scope.state.currentDebtor.ownershipStatus.parent ? $scope.state.currentDebtor.ownershipStatus : null,
                startDateOwnership: $filter('date')($scope.state.currentDebtor.startDateOwnership),
                endDateOwnership: $filter('date')($scope.state.currentDebtor.endDateOwnership)
            };

            /**
             * получение списка статусов по убыванию от родительского к дочернему
             * @param ownershipStatuses
             * @param selectedOwnershipStatusAlias
             * @param tree
             * @returns {*}
             */
            function getTreeOwnershipStatuses(ownershipStatuses, selectedOwnershipStatusAlias, tree) {
                if (!selectedOwnershipStatusAlias) {
                    return null;
                }

                for (var i = 0; i < ownershipStatuses.length; i++) {
                    if (ownershipStatuses[i].children.length) {
                        tree.push(ownershipStatuses[i]);
                        if (Array.isArray(getTreeOwnershipStatuses(ownershipStatuses[i].children, selectedOwnershipStatusAlias, tree))) {
                            return tree;
                        } else {
                            tree.pop();
                        }
                    }

                    if (ownershipStatuses[i].alias === selectedOwnershipStatusAlias) {
                        tree.push(ownershipStatuses[i]);

                        return tree;
                    }
                }
            }
        });





        /**
         * состояние формы
         * @type {{openedDebtorForm: boolean, currentDebtorType: null, currentDebtor: null, action: null}}
         */
        // $scope.state = {
        //     openedDebtorForm: false,
        //     currentDebtorType: null,
        //     currentDebtor: null,
        //     action: null
        // };
//
//         /**
//          * открытие формы добавления|редактирования должников
//          * @param action
//          * @param debtor
//          */
//         $scope.openDebtorForm = function (action, debtor) {
//             $scope.state = {
//                 openedDebtorForm: true,
//                 currentDebtorType: $scope.state.currentDebtorType,
//                 currentDebtor: debtor,
//                 action: action
//             };
//
//             if (action === EDIT) {
//                 $scope.state.currentDebtorType = debtor.type;
//             }
//         };
//
//         /**
//          * закрытие формы добавления|редактирования должников
//          */
//         $scope.closeDebtorForm = function () {
//             $scope.state = {
//                 openedDebtorForm: false,
//                 currentDebtorType: null,
//                 currentDebtor: null,
//                 action: null
//             };
//         };
//
//         /**
//          * форма
//          * @type {{}}
//          */
//         $scope.debtor = {};
//
//         /**
//          * загрузка списка должников и типов должников
//          */
//         $q.all([
//             $http.get(Initializer.Path.AdminDebtorList),
//             $http.get(Initializer.Path.AdminDebtorTypes),
//             $http.get(Initializer.Path.AdminDebtorOwnershipStatuses)
//         ]).then(function (response) {
//             $scope.debtors = response[0].data;
//             $scope.debtorTypes = response[1].data;
//             $scope.ownershipStatues = {
//                 individual: response[2].data,
//                 businessman: _.find(response[2].data, function (status) {
//                     return status.alias == 'owner';
//                 }).children,
//                 legal: _.find(response[2].data, function (status) {
//                     return status.alias == 'owner';
//                 }).children
//             };
//         });
//
//         /**
//          * модели для полей формы
//          * @type {{type: null}}
//          */
//         /*$scope.currentDebtor = {
//             type: null,//тип должника
//             name: null,//фио или наименование организации
//             phone: null,//телефон
//             email: null,//email
//             location: null,//место нахождения или жительства
//             archive: false,//является ли арфивным должник
//             subscriber: false,//является абонентом
//             //физ лицо
//             dateOfBirth: null,//дата рождения физ лица
//             placeOfBirth: null,//место рождения физ лица
//             //индивидуальный предприниматель
//             ogrnip: null,//ОГРНИП
//             inn: null,//ИНН
//             //юр лицо
//             ogrn: null,//ОГРН
//             bossName: null,//ФИО руководителя
//             bossPosition: null,//должность руководителя
//             //статус собственности
//             ownershipStatus: null,//статус собстенности
//             ownershipSubStatus: null,//доп статус
//             shareSize: null,//размер доли
//             ownerName: null,//фио собственника
//             startDateOwnership: null,//дата начала собственности
//             endDateOwnership: null//дата окончания собственности
//         };*/
//
//         /**
//          * отслеживаем изменение типа должника
//          */
//         $scope.$watch('state.currentDebtorType', function (newType) {
//             if (!newType) {
//                 return;
//             }
//
//             if ($scope.state.action === CREATE) {
//                 $scope.currentDebtor = {
//                     name: null,
//                     phone: null,
//                     email: null,
//                     location: null,
//                     archive: false,
//                     subscriber: false,
//                     ownershipStatus: null,
//                     ownershipSubStatus: null,
//                     startDateOwnership: null,
//                     endDateOwnership: null
//                 };
//
//                 switch (newType.alias) {
//                     case 'individual':
//                         $scope.currentDebtor.dateOfBirth = null;
//                         $scope.currentDebtor.placeOfBirth = null;
//                         break;
//                     case 'businessman':
//                         $scope.currentDebtor.ogrnip = null;
//                         $scope.currentDebtor.inn = null;
//                         break;
//                     case 'legal':
//                         $scope.currentDebtor.ogrn = null;
//                         $scope.currentDebtor.inn = null;
//                         $scope.currentDebtor.bossName = null;
//                         $scope.currentDebtor.bossPosition = null;
//                         break;
//                     default:
//                         throw new Error('Undefined debtor type ' + newType.alias);
//                 }
//             }
//
//             if ($scope.state.action === EDIT) {
//                 $scope.currentDebtor = {
//                     name: $scope.state.currentDebtor.name,
//                     phone: parseInt($scope.state.currentDebtor.phone),
//                     email: $scope.state.currentDebtor.email,
//                     location: $scope.state.currentDebtor.location,
//                     archive: $scope.state.currentDebtor.archive,
//                     subscriber: $scope.state.currentDebtor.subscriber,
//                     ownershipStatus: $scope.state.currentDebtor.ownershipStatus.parent ? $scope.state.currentDebtor.ownershipStatus.parent : $scope.state.currentDebtor.ownershipStatus,
//                     ownershipSubStatus: $scope.state.currentDebtor.ownershipStatus.parent ? $scope.state.currentDebtor.ownershipStatus : null,
//                     startDateOwnership: $filter('date')($scope.state.currentDebtor.startDateOwnership),
//                     endDateOwnership: $filter('date')($scope.state.currentDebtor.endDateOwnership)
//                 };
//
//                 switch (newType.alias) {
//                     case 'individual':
//                         $scope.currentDebtor.dateOfBirth = $filter('date')($scope.state.currentDebtor.dateOfBirth);
//                         $scope.currentDebtor.placeOfBirth = $scope.state.currentDebtor.placeOfBirth;
//                         break;
//                     case 'businessman':
//                         $scope.currentDebtor.ogrnip = $scope.state.currentDebtor.ogrnip;
//                         $scope.currentDebtor.inn = $scope.state.currentDebtor.inn;
//                         break;
//                     case 'legal':
//                         $scope.currentDebtor.ogrn = $scope.state.currentDebtor.ogrn;
//                         $scope.currentDebtor.inn = $scope.state.currentDebtor.inn;
//                         $scope.currentDebtor.bossName = $scope.state.currentDebtor.bossName;
//                         $scope.currentDebtor.bossPosition = $scope.state.currentDebtor.bossPosition;
//                         break;
//                     default:
//                         throw new Error('Undefined debtor type ' + newType.alias);
//                 }
//             }
//
//             resetOwnershipSubStatuses();
//         });
//
//         /**
//          * отслеживаем изменение статуса собственности
//          */
//         $scope.$watch('currentDebtor.ownershipStatus', function (newStatus) {
//             if (!newStatus) {
//                 return;
//             }
//
//             //обнуляем доп статусы или заменяем
//             $scope.ownershipSubStatuses = newStatus.children ? newStatus.children : [];
//
//             resetOwnershipSubStatus();
//         });
//
//         /**
//          * отслеживаем изменение доп статуса собстенности
//          */
//         $scope.$watch('currentDebtor.ownershipSubStatus', function (newStatus) {
//             if (!newStatus) {
//                 return;
//             }
//
//             resetAdditionalOwnershipStatus();
//         });
//
//         /**
//          * сброс списка доп статусов
//          */
//         function resetOwnershipSubStatuses() {
//             //сбрасываем список доп статусов
//             $scope.ownershipSubStatuses = [];
//             resetOwnershipSubStatus();
//         }
//
//         /**
//          * сброс доп статуса и доп параметров
//          */
//         function resetOwnershipSubStatus() {
//             //сбрасываем доп статус из модели
//             $scope.currentDebtor.ownershipSubStatus = null;
//             resetAdditionalOwnershipStatus();
//         }
//
//         /**
//          * сброс доп параметров статусов собственности
//          */
//         function resetAdditionalOwnershipStatus() {
//             //сбрасываем размер доли
//             $scope.currentDebtor.shareSize = null;
//             //сбрасываем фио собственника
//             $scope.currentDebtor.ownerName = null;
//         }
//
//         $scope.submit = function ($event, form) {
//             $event.preventDefault();
// console.log($scope.currentDebtor);
//             FormHelper.forceDirty(form);
//
//             if (form.$invalid) {
//                 return;
//             }
//
//             console.log($scope.currentDebtor);
//         };
    }

})(angular);