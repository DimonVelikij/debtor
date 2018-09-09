(function (angular) {
    "use strict";

    angular
        .module('cms.flat')
        .controller('SubscriberCtrl', SubscriberCtrl);

    SubscriberCtrl.$inject = [
        '$scope',
        '$http',
        '$rootScope',
        '$filter',
        '_',
        'Initializer',
        'FormHelper'
    ];

    function SubscriberCtrl(
        $scope,
        $http,
        $rootScope,
        $filter,
        _,
        Initializer,
        FormHelper
    ) {
        /**
         * форма
         * @type {{}}
         */
        $scope.subscriber = {};

        /**
         * открытие формы создание|редактирование абонента
         * @param subscriber
         */
        $scope.openSubscriberForm = function (subscriber) {
            $scope.state = {
                openedSubscriberForm: true,
                currentSubscriber: {}
            };

            if (subscriber) {
                $scope.state.currentSubscriber = {
                    id: subscriber.id,
                    name: subscriber.name,
                    personalAccount: subscriber.personalAccount.account,
                    phone: parseInt(subscriber.phone),
                    email: subscriber.email,
                    dateDebt: $filter('date')(subscriber.dateDebt),
                    dateOpenAccount: $filter('date')(subscriber.personalAccount.dateOpenAccount),
                    dateCloseAccount: $filter('date')(subscriber.personalAccount.dateCloseAccount)
                };
            }

            //если добавляем нового абонента и уже есть абоненты - подставляем данные л\с из первого
            if (!subscriber && $scope.subscribers.length) {
                $scope.state.currentSubscriber = {
                    personalAccount: $scope.subscribers[0].personalAccount.account,
                    dateOpenAccount: $filter('date')($scope.subscribers[0].personalAccount.dateOpenAccount),
                    dateCloseAccount: $filter('date')($scope.subscribers[0].personalAccount.dateCloseAccount)
                };
            }
        };

        /**
         * закрытие формы создание|редактирование абонента
         * @param form
         */
        $scope.closeSubscriberForm = function (form) {
            $scope.state = {
                openedSubscriberForm: false,
                currentSubscriber: {}
            };

            FormHelper.forcePristine(form);
        };

        /**
         * загрузка списка абонентов
         */
        $http.get(Initializer.Path.AdminSubscriberList).then(function (response) {
            $scope.subscribers = response.data;
        });

        /**
         * сохранение фомры абонента
         * @param $event
         * @param form
         */
        $scope.submit = function ($event, form) {
            $event.preventDefault();

            FormHelper.forceDirty(form);

            if (form.$invalid) {
                return;
            }

            var submitData = {
                flatId: Initializer.Settings.FlatId,
                id: $scope.state.currentSubscriber.id,
                name: $scope.state.currentSubscriber.name,
                personalAccount: $scope.state.currentSubscriber.personalAccount,
                phone: $scope.state.currentSubscriber.phone,
                email: $scope.state.currentSubscriber.email,
                dateDebt: $scope.state.currentSubscriber.dateDebt ? $scope.state.currentSubscriber.dateDebt.replace(/\./g, '') : null,
                dateOpenAccount: $scope.state.currentSubscriber.dateOpenAccount ? $scope.state.currentSubscriber.dateOpenAccount.replace(/\./g, '') : null,
                dateCloseAccount: $scope.state.currentSubscriber.dateCloseAccount ? $scope.state.currentSubscriber.dateCloseAccount.replace(/\./g, '') : null
            };

            $http.post(Initializer.Path.AdminSubmitSubscriber, submitData)
                .then(function (response) {
                    if (response.data.success) {
                        if (!$scope.state.currentSubscriber.id) {
                            //добавляем в список нового абонента
                            $scope.subscribers.push(response.data.subscriber);
                            //отдаем список л\с в контроллер с должниками, чтобы не пришлось обновлять страницу
                            var personalAccounts = [];
                            _.forEach($scope.subscribers, function (subscriber) {
                                var exist = _.filter(personalAccounts, function (personalAccount) {
                                    return personalAccount.account === subscriber.personalAccount.account;
                                });

                                if (!exist.length) {
                                    personalAccounts.push(subscriber.personalAccount);
                                }
                            });

                            $rootScope.$broadcast('personalAccounts', {
                                personalAccounts: personalAccounts
                            })
                        } else {
                            _.forEach($scope.subscribers, function (subscriber, index) {
                                if ($scope.subscribers[index].id === response.data.subscriber.id) {
                                    $scope.subscribers[index] = response.data.subscriber;
                                }
                            });
                        }
                        $scope.closeSubscriberForm(form);
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