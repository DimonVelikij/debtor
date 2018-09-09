(function (angular) {
    "use strict";

    angular
        .module('cms.flat')
        .controller('DebtorCtrl', DebtorCtrl);

    DebtorCtrl.$inject = [
        '$scope',
        '$http',
        '$rootScope',
        '$q',
        '$filter',
        '_',
        'Initializer',
        'FormHelper',
        '$controller'
    ];

    function DebtorCtrl(
        $scope,
        $http,
        $rootScope,
        $q,
        $filter,
        _,
        Initializer,
        FormHelper,
        $controller
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
         * @param form
         */
        $scope.closeDebtorForm = function (form) {
            $scope.state = {
                openedDebtorForm: false,
                type: null,
                currentDebtor: {},
                isChangeOwnershipStatus: false
            };

            //сбрасываем доп статусы
            $scope.ownershipSubStatuses = null;
            //сбрасываем объект должника
            $scope.currentDebtor = {};

            FormHelper.forcePristine(form);
        };

        /**
         * загрузка списка должников, типов должников, статусов собственности и лицевых счетов
         */
        $q.all([
            $http.get(Initializer.Path.AdminDebtorList),
            $http.get(Initializer.Path.AdminDebtorTypes),
            $http.get(Initializer.Path.AdminDebtorOwnershipStatuses),
            $http.get(Initializer.Path.AdminPersonalAccounts)
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
            $scope.personalAccounts = response[3].data;
        });

        //ловим список л\с из контроллера с абонентами
        $rootScope.$on('personalAccounts', function (event, data) {
            $scope.personalAccounts = data.personalAccounts;
        });

        /**
         * подстановка контроллера в зависимости от типа должника
         * @returns {*}
         * @constructor
         */
        $scope.DebtorSubCtrl = function () {
            if (!$scope.state || !$scope.state.type) {
                return null;
            }

            var ctrlList = {
                individual: 'DebtorIndividualCtrl',
                businessman: 'DebtorBusinessmanCtrl',
                legal: 'DebtorLegalCtrl'
            };

            return $controller(ctrlList[$scope.state.type.alias], {$scope: $scope}).constructor;
        };

        /**
         * отслеживаем изменение типа должника
         */
        $scope.$watch('state.type', function (newDebtorType) {
            if (!newDebtorType) {
                return;
            }

            //при смене типа подменяем контроллер
            $scope.DebtorSubCtrl();

            //получаем статусы собственности от родительского к дочернему
            var treeStatuses = $scope.state.currentDebtor.ownershipStatus ?
                getTreeOwnershipStatuses($scope.ownershipStatuses[newDebtorType.alias], $scope.state.currentDebtor.ownershipStatus.alias, []) :
                [];

            //проставляем общие данные
            $scope.currentDebtor = {
                id: $scope.state.currentDebtor.id ? $scope.state.currentDebtor.id : null,//id дожника
                name: $scope.state.currentDebtor.name,//имя
                personalAccount: $scope.state.currentDebtor.personalAccount,//лицевой счет
                phone: $scope.state.currentDebtor.phone ? parseInt($scope.state.currentDebtor.phone) : null,//телефон
                email: $scope.state.currentDebtor.email,//email
                location: $scope.state.currentDebtor.location,//место нахождения или жительства
                archive: $scope.state.currentDebtor.archive ? $scope.state.currentDebtor.archive : false,//является ли архивным должник
                ownershipStatus: treeStatuses.length ? treeStatuses[0] : undefined,//статус собстенности
                ownershipSubStatus: treeStatuses.length && treeStatuses.length > 1 ? treeStatuses[1] : undefined,//доп статус собственности
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

            //при смене должника очищаем список дочерних статусов
            $scope.ownershipSubStatuses = null;

            //чекбоксы в сонате не работают через ng-model - приходится делать через iCheck и эвент ifChanged
            //устанавливаем или снимаем чекбокс "Архивный"
            angular.element('#archive').iCheck($scope.currentDebtor.archive ? 'check' : 'uncheck');

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
        });

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

        /**
         * вывод статуса собственности в списке должников
         * @param debtor
         * @returns {string}
         */
        $scope.showOwnershipStatus = function (debtor) {
            if (!debtor || !debtor.isShow) {
                return;
            }

            var tree = getTreeOwnershipStatuses($scope.ownershipStatuses[debtor.type.alias], debtor.ownershipStatus.alias, []);

            return tree.length > 1 ? tree[0].title + ' - ' + tree[1].title : tree[0].title;
        };

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
         * @returns {{name: *, phone: *, email: *, location: *, archive: (boolean|*), ownershipStatus: (null|*), startDateOwnership: (null|*), endDateOwnership: (null|*)}}
         */
        $scope.getBaseSubmitData = function () {
            var baseSubmitData = {
                flatId: Initializer.Settings.FlatId,
                id: $scope.currentDebtor.id,
                type: $scope.state.type,
                personalAccount: $scope.currentDebtor.personalAccount,
                name: $scope.currentDebtor.name,
                phone: $scope.currentDebtor.phone,
                email: $scope.currentDebtor.email,
                location: $scope.currentDebtor.location,
                archive: $scope.currentDebtor.archive,
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
                    baseSubmitData.ownerName = $scope.currentDebtor.ownerName;
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
                            $scope.debtors.push(response.data.debtor);
                        } else {
                            //меняем инофрмацию о должнике
                            _.forEach($scope.debtors, function (debtor, index) {
                                if ($scope.debtors[index].id === response.data.debtor.id) {
                                    $scope.debtors[index] = response.data.debtor;
                                }
                            });
                        }
                        $scope.closeDebtorForm(form);
                    } else {
                        FormHelper.showBackendErrors(response.data.errors, form);
                    }
                }, function (error) {

                })
                .finally(function () {

                });
        };

        /**
         * отслеживаем изменение чекбокса "Архивный"
         */
        angular.element('#archive').on('ifChanged', function (event) {
            $scope.currentDebtor.archive = event.target.checked;
        });
    }

})(angular);