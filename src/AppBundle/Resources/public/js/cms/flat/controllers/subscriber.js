(function (angular) {
    "use strict";

    angular
        .module('cms.flat')
        .controller('SubscriberCtrl', SubscriberCtrl);

    SubscriberCtrl.$inject = [
        '$scope',
        '$http',
        '$filter',
        '_',
        'Initializer',
        'FormHelper'
    ];

    function SubscriberCtrl(
        $scope,
        $http,
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
                    phone: parseInt(subscriber.phone),
                    email: subscriber.email,
                    dateDebt: $filter('date')(subscriber.dateDebt),
                    sumDebt: subscriber.sumDebt,
                    sumFine: subscriber.sumFine
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
                phone: $scope.state.currentSubscriber.phone,
                email: $scope.state.currentSubscriber.email,
                dateDebt: $scope.state.currentSubscriber.dateDebt ? $scope.state.currentSubscriber.dateDebt.replace(/\./g, '') : null,
                sumDebt: parseFloat($scope.state.currentSubscriber.sumDebt),
                sumFine: parseFloat($scope.state.currentSubscriber.sumFine)
            };

            $http.post(Initializer.Path.AdminSubmitSubscriber, submitData)
                .then(function (response) {
                    if (response.data.success) {
                        if (!$scope.state.currentSubscriber.id) {
                            //добавляем в список нового абонента
                            $scope.subscribers.push(response.data.subscriber);
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
        }
    }

})(angular);