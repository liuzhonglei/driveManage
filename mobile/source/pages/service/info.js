/**
 * Created by jf on 15/12/10.
 */

"use strict";

import FontAwesome from 'react-fontawesome';
import WeUI from 'react-weui';
const {Button, Cells, Cell, CellHeader,CellsTitle, CellBody, CellFooter,Toast,Dialog}  = WeUI;
import Page from '../../component/page';
const {Alert} = Dialog;

import './index.less';
import Controller from '../../component/controller'

/**
 * 救援登记界面
 */
export default class ServiceInfo extends Controller {

    /**
     * 加载前
     */
    componentWillMount() {
        super.componentWillMount();
        this.state.info = {
            token: null
        }

        // 显示推荐配置
        this.showLoading();

        $.ajax({
            url: './index.php?s=/addon/School/School/getSchoolInfo',
            type: 'GET',
            data: {
                query_token: this.props.routeParams.token,
                isAjax: true
            },
            success: function (response) {
                //转换条件
                if (response.condition) {
                    var conditions = response.condition.split(',');
                    response.condition = "";
                    var conditionMap = {
                        1: "可夜间练车",
                        2: "可接送",
                        3: "可刷卡",
                        4: "是否速成",
                        5: "可考自动档",
                        6: "可分期"
                    };
                    response.condition = "";
                    for (var i in conditions) {
                        response.condition += " " + conditionMap[conditions[i]];
                    }
                }

                // 转换地点
                if (response.recruit_place) {
                    var placeMap = {
                        1: "思明区",
                        2: "湖里区",
                        3: "集美区",
                        4: "同安区",
                        5: "海沧区",
                        6: "翔安区"
                    };
                    var recruit_place_list = response.recruit_place.split(',');
                    response.recruit_place = "";
                    for (var i in recruit_place_list) {
                        response.recruit_place += " " + placeMap[recruit_place_list[i]];
                    }
                }

                // 课程
                this.state.course = [];
                for (var i = 0; i < response.course.length; i++) {
                    this.state.course.push(
                        <Cell>
                            <CellHeader className="half_header">{response.course[i].name}</CellHeader>
                            <CellBody>
                                {response.course[i].sign_charge}&nbsp;(原价<span
                                style={{textDecoration:"line-through"}}>{parseInt(response.course[i].sign_charge) + 200}</span>)
                            </CellBody>
                        </Cell>
                    );
                }

                // 设置
                this.setState({info: response});
                this.stopLoading();
            }.bind(this)
        });
    }


    /**
     * 加载
     */
    componentDidMount() {
        //$("[name='rate']").raty({starType: 'i', number: 5, score: this.state.info.apprise_level, readOnly: true});
    }

    /**
     * 到报名界面
     */
    toRegisterPage() {
        location.href = "#/user/register/" + this.state.info.token;
    }


    /**
     * 显示当前位置
     */
    showPosition() {
        console.log('this.state.info', this.state.info);
        if (!this.state.info.coordinate && this.state.info.sign_place) {
            this.state.info.coordinate = this.state.info.sign_place.coordinate
        }

        var coordinates = this.state.info.coordinate.split(",");

        console.log('coordinates', coordinates);

        wx.openLocation({
            longitude: coordinates[0], // 经度，浮点数，范围为180 ~ -180。
            latitude: coordinates[1], // 纬度，浮点数，范围为90 ~ -90
            name: this.state.info.name, // 位置名
            address: this.state.info.sign_place.address, // 地址详情说明
            scale: 20, // 地图缩放级别,整形值,范围从1~28。默认为最大
            infoUrl: '' // 在查看位置界面底部显示的超链接,可点击跳转
        });
    }

    /**
     * 渲染
     * @returns {XML}
     */
    render() {
        return (
            <div className="page">
                <Toast icon="loading" show={this.state.showLoading}>正在加载中...</Toast>
                <Alert title={this.state.alert.title} buttons={this.state.alert.buttons} show={this.state.showAlert}>
                    {this.state.alert.message}
                </Alert>
                <div className="weui_tab">
                    <div className="weui_tab_bd">
                        <div className="images_wrapper">
                            <img src={this.state.info.photo}
                                 style={{display:"block",border:0, width: "100%",height: "auto",maxidth: "100%"}}
                            />

                            <div className="images_content">&nbsp;&nbsp;{this.state.info.name}</div>
                        </div>
                        <Cells access style={{marginTop:0}}>
                            <Cell>
                                <CellBody>
                                    <div onClick={this.showPosition.bind(this)}
                                         style={{float:"left", display: "block",width:"50%",textAlign:"left"}}>
                                        <FontAwesome style={{color:"#4397f1"}} name='location-arrow'
                                                     size="lg"/>
                                        &nbsp;
                                        <span style={{color:"#4397f1"}}>去报名点</span>
                                    </div>
                                    <a href={this.state.info.phone? 'tel:'+this.state.info.phone:"javascript:"}

                                       style={{float:"right",display: "block",width:"50%",textAlign:"left"}}>
                                        <FontAwesome style={{color:"orange"}} name='phone'
                                                     size="lg"/>
                                        &nbsp;
                                        <span style={{color:"#4397f1"}}>联系电话</span>
                                    </a>
                                </CellBody>
                            </Cell>
                            <Cell>
                                <CellHeader > <FontAwesome style={{color:"orange"}} name='map-marker'
                                                           size="lg"/></CellHeader>
                                <CellBody >
                                    &nbsp;
                                    {this.state.info.address}
                                </CellBody>
                            </Cell>
                            <Cell>
                                <CellHeader className="half_header">招生地点</CellHeader>
                                <CellBody>
                                    <span>{this.state.info.recruit_place}</span>
                                </CellBody>
                            </Cell>
                            <Cell>
                                <CellHeader className="half_header">基本条件</CellHeader>
                                <CellBody>
                                    <span>{this.state.info.condition}</span>
                                </CellBody>
                            </Cell>
                        </Cells>
                        <CellsTitle>课程</CellsTitle>
                        <Cells>
                            {this.state.course}
                        </Cells>
                        <CellsTitle>提供服务</CellsTitle>
                        <Cells access>
                            <Cell href={"#/service/info/"+this.props.routeParams.token+"/map"}>
                                <CellHeader className="icon_nav">
                                    <FontAwesome style={{color:"black"}} name='map-pin'
                                                 size="lg"/></CellHeader>
                                <CellBody>
                                    学车场地
                                </CellBody>
                                <CellFooter>
                                </CellFooter>
                            </Cell>
                            <Cell
                                href={"index.php?s=/addon/School/School/schoolQuestion/token/"+this.state.info.token+".html"}>
                                <CellHeader className="icon_nav">
                                    <FontAwesome style={{color:"black"}} name='question'
                                                 size="lg"/></CellHeader>

                                <CellBody>
                                    <span>在线问答</span>
                                </CellBody>
                                <CellFooter>
                                </CellFooter>
                            </Cell>
                            <Cell style={{display: this.state.info.scene_url? "":"none"}}
                                  href={this.state.info.scene_url}>
                                <CellHeader className="icon_nav">
                                    <FontAwesome style={{color:"black"}} name='film'
                                                 size="lg"/></CellHeader>
                                <CellBody>
                                    3D影像
                                </CellBody>
                                <CellFooter>
                                </CellFooter>
                            </Cell>
                        </Cells>
                    </div>
                    <div className="weui_tabbar" style={{position:"fixed"}}>
                        <Button onClick={this.toRegisterPage.bind(this,event)}>报名</Button>
                    </div>
                </div>
            </div>
        )
            ;
    }
};
