监听数据变化

scope提供$watch API（http://code.angularjs.org/1.0.2/docs/api/ng.$rootScope.Scope#$watch），用于监测model的变化。

$rootScope.$watch('items',function(){
                                $rootScope.count++;
                            })
