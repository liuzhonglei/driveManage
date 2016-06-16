import React from 'react';
import {Router, Route} from 'react-router';
import 'weui';

import UserRegister from './pages/user/register';


import ServiceList from './pages/service/list';
import ServiceMap from './pages/service/map';
import ServiceInfo from './pages/service/info';
import ServiceInfoMap from './pages/service/info_map';


ReactDOM.render((
    <Router>
        <Route path="/" component={ServiceList}/>

        <Route path="/user/register/:token" component={UserRegister}/>

        <Route path="/service/List" component={ServiceList}/>
        <Route path="/service/Map" component={ServiceMap}/>
        <Route path="/service/info/:token" component={ServiceInfo}/>
        <Route path="/service/info/:token/map" component={ServiceInfoMap}/>

    </Router>
), document.getElementById('container'));
