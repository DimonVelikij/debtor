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
         * открытие формы создания|редактирования должника
         * @param debtor
         */
        $scope.openDebtorForm = function (debtor) {
            $scope.state = {
                openedDebtorForm: true,
                type: debtor ? debtor.type : null,
                currentDebtor: debtor ? debtor : {},
                isChangeOwnershipStatus: false
            };
        };

        /**
         * закрытие формы создания|редактирования должника
         */
        $scope.closeDebtorForm = function () {
            $scope.state = {
                openedDebtorForm: false,
                type: null,
                currentDebtor: {},
                isChangeOwnershipStatus: false
            };

            $scope.ownershipSubStatuses = null;
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
            $scope.types = response[1].data;

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

        /**
         * отслеживаем изменение типа должника
         */
        $scope.$watch('state.type', function (newDebtorType) {
            if (!newDebtorType) {
                return;
            }

            var treeStatuses = $scope.state.currentDebtor.ownershipStatus ?
                getTreeOwnershipStatuses($scope.ownershipStatuses[newDebtorType.alias], $scope.state.currentDebtor.ownershipStatus.alias, []) :
                [];

            //проставляем общие данные
            $scope.currentDebtor = {
                id: $scope.state.currentDebtor.id ? $scope.state.currentDebtor.id : null,//id дожника
                name: $scope.state.currentDebtor.name,//имя
                phone: $scope.state.currentDebtor.phone ? parseInt($scope.state.currentDebtor.phone) : null,//телефон
                email: $scope.state.currentDebtor.email,//email
                location: $scope.state.currentDebtor.location,//место нахождения или жительства
                archive: $scope.state.currentDebtor.archive ? $scope.state.currentDebtor.archive : false,//является ли арфивным должник
                subscriber: $scope.state.currentDebtor.subscriber ? $scope.state.currentDebtor.subscriber : false,//является абонентом
                ownershipStatus: treeStatuses.length ? treeStatuses[0] : null,//статус собстенности
                ownershipSubStatus: treeStatuses.length && treeStatuses.length > 1 ? treeStatuses[1] : null,//доп статус собственности
                startDateOwnership: $scope.state.currentDebtor.startDateOwnership ? $filter('date')($scope.state.currentDebtor.startDateOwnership) : null,//дата начала собственности
                endDateOwnership: $scope.state.currentDebtor.endDateOwnership ? $filter('date')($scope.state.currentDebtor.endDateOwnership) : null,//дата окончания собственности
                //доп параметры, зависящие от статусов собственности
                shareSize: $scope.state.currentDebtor.shareSize,//размер доли
                ownerName: $scope.state.currentDebtor.ownerName,//фио собственника
                //сбрасываем параметры
                //физ лицо
                dateOfBirth: null,//дата рождения физ лица
                placeOfBirth: null,//место рождения физ лица
                //индивидуальный предприниматель
                ogrnip: null,//ОГРНИП
                inn: null,//ИНН
                //юр лицо
                ogrn: null,//ОГРН
                bossName: null,//ФИО руководителя
                bossPosition: null//должность руководителя
            };

            //проставляем данные в зависимости от типа должника
            switch (newDebtorType.alias) {
                case 'individual':
                    $scope.currentDebtor.dateOfBirth = $scope.state.currentDebtor.dateOfBirth ? $filter('date')($scope.state.currentDebtor.dateOfBirth) : null;//дата рождения
                    $scope.currentDebtor.placeOfBirth = $scope.state.currentDebtor.placeOfBirth;//место рождения
                    break;
                case 'businessman':
                    $scope.currentDebtor.ogrnip = $scope.state.currentDebtor.ogrnip;//ОГРНИП
                    $scope.currentDebtor.inn = $scope.state.currentDebtor.inn;//ИНН
                    break;
                case 'legal':
                    $scope.currentDebtor.ogrn = $scope.state.currentDebtor.ogrn;//ОГРН
                    $scope.currentDebtor.inn = $scope.state.currentDebtor.inn;//ИНН
                    $scope.currentDebtor.bossName = $scope.state.currentDebtor.bossName;//ФИО руководителя
                    $scope.currentDebtor.bossPosition = $scope.state.currentDebtor.bossPosition;//должность руководителя
                    break;
                default:
                    throw new Error("Undefined debtor type '" + newDebtorType.alias + "'");
            }

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
         * отслеживаем изменение стауса собственности
         */
        $scope.$watch('currentDebtor.ownershipStatus', function (newOwnershipStatus) {
            if (!newOwnershipStatus) {
                return;
            }

            //получаем список дочерних статусов
            $scope.ownershipSubStatuses = newOwnershipStatus.children ? newOwnershipStatus.children : [];

            if ($scope.state.isChangeOwnershipStatus) {
                //сбрасываем дочерние статусы
                $scope.currentDebtor.ownershipSubStatus = null;
            }

            $scope.state.isChangeOwnershipStatus = true;
        });

        /**
         * данные для отправки на backend для всех типов должников
         * @returns {{name: *, phone: *, email: *, location: *, archive: (boolean|*), subscriber: (boolean|*), ownershipStatus: (null|*), startDateOwnership: (null|*), endDateOwnership: (null|*)}}
         */
        $scope.getBaseSubmitData = function () {
            var baseSubmitData = {
                flatId: Initializer.Settings.FlatId,
                id: $scope.currentDebtor.id,
                type: $scope.state.type,
                name: $scope.currentDebtor.name,
                phone: $scope.currentDebtor.phone,
                email: $scope.currentDebtor.email,
                location: $scope.currentDebtor.location,
                archive: $scope.currentDebtor.archive,
                subscriber: $scope.currentDebtor.subscriber,
                ownershipStatus: $scope.currentDebtor.ownershipSubStatus ? $scope.currentDebtor.ownershipSubStatus : $scope.currentDebtor.ownershipStatus,
                startDateOwnership: $scope.currentDebtor.startDateOwnership ? $scope.currentDebtor.startDateOwnership.replace(/\./g, '') : null,
                endDateOwnership: $scope.currentDebtor.endDateOwnership ? $scope.currentDebtor.endDateOwnership.replace(/\./g, '') : null
            };

            if (baseSubmitData.ownershipStatus) {
                if (
                    baseSubmitData.ownershipStatus.alias === 'owner_shared' ||
                    baseSubmitData.ownershipStatus.alias === 'legal_representative_underage_owner_shared'
                ) {
                    baseSubmitData.shareSize = $scope.currentDebtor.shareSize;
                }

                if (
                    baseSubmitData.ownershipStatus.alias === 'legal_representative_underage_owner_individual' ||
                    baseSubmitData.ownershipStatus.alias === 'legal_representative_underage_owner_shared' ||
                    baseSubmitData.ownershipStatus.alias === 'legal_representative_underage_owner_joint'
                ) {
                    baseSubmitData.ownerName = $scope.state.currentDebtor.ownerName;
                }
            }

            return baseSubmitData;
        };

        /**
         * отправка запроса на сохранение|обновление должника
         * @param form
         * @param submitData
         */
        $scope.baseSubmit = function (form, submitData) {
            $http.post(Initializer.Path.AdminSubmitDebtor, submitData)
                .then(function (response) {
                    if (response.data.success) {
                        if (!$scope.currentDebtor.id) {
                            //добавляем в список нового должника
                            $scope.debtors.unshift(response.data.debtor);
                        } else {
                            //меняем инофрмацию о должнике
                            _.forEach($scope.debtors, function (debtor, index) {
                                if ($scope.debtors[index].id === response.data.debtor.id) {
                                    $scope.debtors[index] = response.data.debtor;
                                }
                            });
                        }
                        $scope.closeDebtorForm();
                    } else {
                        FormHelper.showBackendErrors(response.data.errors, form);
                    }
                }, function (error) {

                })
                .finally(function () {

                });
        };
    }

})(angular);