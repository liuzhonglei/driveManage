/**
 * Created by jf on 15/12/10.
 */

"use strict";

import WeUI from 'react-weui';
import FontAwesome from 'react-fontawesome';

const {ButtonArea, Button,CellsTitle, Cells, Cell, CellHeader, CellBody, CellFooter,Dialog,Toast}  = WeUI;
import '../../component/page.less';
import './user.less';

import Controller from '../../component/controller'

import Time0 from './images/time0.png';
import Time1 from './images/time1.png';
import Mileage0 from './images/mileage0.png';
import Mileage1 from './images/mileage1.png';


const {Alert} = Dialog;
export default class UserRegister extends Controller {

    /**
     * 查询数据
     */
    componentWillMount() {
        super.componentWillMount();

        // 取值
        this.state.info = {
            period_value: 0,
            mileage_value: 0
        };
        this.state.loadNum = 2;


        // 查询当前用户
        this.showLoading();
        $.ajax({
            url: '/data/user_info',
            type: 'GET',
            success: function (response) {
                if (response.success == true) {
                    if (response.result.type == 'P') {
                        this.state.showAlert = true;
                        this.state.alert.message = "只有个体车主可以修改周期！";
                        this.state.alert.title = '提示信息';
                        this.state.alert.buttons = [
                            {
                                type: 'primary',
                                label: '确认',
                                onClick: function () {
                                    window.location.href = '/#/archive/info';
                                }.bind(this)
                            }
                        ]
                        this.setState(this.state);
                    }
                } else {
                    this.showAlert(response.message);
                }

            }.bind(this),
            error: this.error_handle.bind(this)
        });


        // 显示推荐配置
        this.showLoading();
        $.ajax({
            url: '/data/common',
            type: 'POST',
            data: {
                source: 'maintain/maintenance_configs/'
            },
            success: function (response) {
                if (response.success == true) {
                    response.result.sort(function (a, b) {
                        return a.sort > b.sort ? 1 : -1
                    });
                    this.setState({suggest: response.result});
                    this.refreshSuggest();
                } else {
                    this.showAlert(response.message);
                }


                this.state.loadNum--;
                if (this.state.loadNum == 0) {
                    this.stopLoading();
                }
            }.bind(this),
            error: this.error_handle.bind(this)
        });

        // 查询当前车辆配置信息
        var url = '/data/maintain_info';
        if (this.props.routeParams.id) {
            url += "/" + this.props.routeParams.id
        }
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                console.log('response', response);
                if (response.success == true) {
                    this.state.info = response.result;
                    this.state.info.period_value = response.result.second_maintenance.time_cycle? response.result.second_maintenance.time_cycle:0;
                    this.state.info.mileage_value = response.result.second_maintenance.mileage_cycle?response.result.second_maintenance.mileage_cycle:0;

                    this.setState({info: response.result});
                    this.refreshSuggest();
                } else {
                    this.showAlert(response.message);
                }
                this.state.loadNum--;
                if (this.state.loadNum == 0) {
                    this.stopLoading();
                }
            }.bind(this),
            error: this.error_handle.bind(this)
        });


        // 限制加减的方式
        this.state.limit = "1";
        this.state.timeUnit = 5;
        this.state.mileageUnit = 100;

        this.state.unit = "日";
        this.state.refer = [{describe: " 小型客车(含乘用车)<br/>车长16m", value: "90日<br/>(上限120日)"}]
    }

    /**
     * 更新策略
     */
    refreshSuggest() {
        // 加入
        this.state.items = [];

        //排序列表
        this.state.suggest.sort(this.sortNumber);

        //刷新建议
        for (var i = 0; i < this.state.suggest.length; i++) {
            this.state.items.push(
                <Cell
                    onClick={this.setValue.bind(this, this.state.suggest[i].recommended_value,this.state.suggest[i+1].recommended_value)}>
                    <CellBody style={{fontSize:'0.8em'}}>
                        {this.state.suggest[i].title}<br/>{this.state.suggest[i].desc}
                    </CellBody>
                    <CellFooter style={{fontSize:'1.2em'}}>
                        <img style={{width:'1.2em'}}
                             src={Time1}/> {this.state.suggest[i].recommended_value + "日"}
                        &nbsp;&nbsp;<img style={{width:'1.2em'}}
                                         src={Mileage1}/> {this.state.suggest[i + 1].recommended_value + "千米"}
                    </CellFooter>
                </Cell>
            );
            i++;
        }

        // 设置
        this.setState({items: this.state.items});
    }

    /**
     * 策略排序
     * @param a
     * @param b
     * @returns {number}
     */
    sortNumber(a, b) {
        var count = 0;
        if (b.title == a.title) {
            count = a.recommended_value - b.recommended_value;
        } else {
            count = b.title.length.length - a.title.length;
        }

        return count;
    }

    /**
     * 提交修改
     */
    submit() {
        var url = '/data/maintain_info';
        if (this.props.routeParams.id) {
            url += "/" + this.props.routeParams.id
        }
        this.showLoading();

        $.ajax({
            url: url,
            type: 'POST',
            data: this.state.info,
            success: function (response) {
                this.stopLoading();
                if (response.success == true) {
                    this.setState({showToast: true});
                    this.state.toastTimer = setTimeout(()=> {
                            this.setState({showToast: false});
                            this.stopLoading();

                            if (this.props.routeParams.id) {
                                window.location.href = '#/archive/info/' + this.props.routeParams.id;
                            } else {
                                window.location.href = '#/archive/info';
                            }
                        }
                        , 2000
                    )
                } else {
                    this.showAlert(response.msg);
                }
            }.bind(this),
            error: this.error_handle.bind(this)
        });
    }

    /**
     * 关闭界面
     */
    componentWillUnmount() {
        this.state.toastTimer && clearTimeout(this.state.toastTimer);
    }

    /**
     * 设置数值
     * @param value
     */
    setValue(period_value, mileage_value) {
        this.state.info.period_value = period_value;
        this.state.info.mileage_value = mileage_value;
        this.setState({info: this.state.info});
    }


    /**
     * 数值增加
     */
    valuePlus(type) {
        if (type == "time") {
            this.state.info.period_value = parseInt(this.state.info.period_value) + this.state.timeUnit;
        } else {
            this.state.info.mileage_value = parseInt(this.state.info.mileage_value) + this.state.mileageUnit;
        }
        this.setState({info: this.state.info});
    }

    /**
     * 数值减少
     */
    valueMinus(type) {

        if (type == "time") {
            if (parseInt(this.state.info.period_value) - this.state.timeUnit <= 0) {
                return;
            }
            this.state.info.period_value = parseInt(this.state.info.period_value) - this.state.timeUnit;
        } else {
            if (parseInt(this.state.info.mileage_value) - this.state.mileageUnit <= 0) {
                return;
            }
            this.state.info.mileage_value = parseInt(this.state.info.mileage_value) - this.state.mileageUnit;
        }
        this.setState({info: this.state.info});
    }


    /**
     * 渲染
     * @returns {XML}
     */
    render() {
        return (
            <div className="page">
                <Toast show={this.state.showToast}>保存成功</Toast>
                <Toast icon="loading" show={this.state.showLoading}>正在加载中...</Toast>
                <Alert title={this.state.alert.title} buttons={this.state.alert.buttons} show={this.state.showAlert}>
                    {this.state.alert.message}
                </Alert>
                <Cells style={{marginTop:'0px'}}>
                    <Cell>

                        <CellBody style={{textAlign:'center',fontSize:"2em"}}>
                            {this.state.info.plate}
                        </CellBody>

                    </Cell>
                    <Cell>
                        <div className="item-div" style={{}}>
                            <img src={Time1}/>
                                    <span
                                        className="text"
                                        style={{ display: 'inline'}}>时间</span><br/><span>(日)</span>
                        </div>
                        <div className="item-div">
                            <FontAwesome className=" fa-minus"
                                         onClick={this.valuePlus.bind(this,'time')}
                                         name='caret-up' size="2x"
                                         style={{color:"#049FF1"}}/>
                                    <span className="" style={{fontSize:"1.5em"}}>
                                        {this.state.info.period_value?this.state.info.period_value:0}
                                    </span>
                            <FontAwesome className=" fa-minus"
                                         onClick={this.valueMinus.bind(this,'time')}
                                         name='caret-down' size="2x"
                                         style={{color:"#049FF1"}}/>
                        </div>
                        <div
                            style={{backgroundAttachment:"fixed",backgroundPosition:"center",backgroundColor: "gray", width: "2px",height:"6em"}}></div>
                        <div className="item-div">
                            <img src={Mileage1}/>
                                        <span
                                            className="text"
                                            style={{ display: 'inline'}}>里程</span><br/><span>(千米)</span>
                        </div>
                        <div className="item-div">
                            <FontAwesome className=" fa-minus"
                                         onClick={this.valuePlus.bind(this,'mileage')}
                                         name='caret-up' size="2x"
                                         style={{color:"#049FF1"}}/>
                                    <span className="" style={{fontSize:"1.5em"}}>
                                        {this.state.info.mileage_value?this.state.info.mileage_value:0}
                                    </span>
                            <FontAwesome className=" fa-minus"
                                         onClick={this.valueMinus.bind(this,'mileage')}
                                         name='caret-down' size="2x"
                                         style={{color:"#049FF1"}}/>
                        </div>
                    </Cell>
                    <Cell>

                        <CellBody style={{textAlign:'center',fontSize:"0.7em"}}>
                            维护周期以行驶里程间隔或行驶时间间隔先到者为准！
                        </CellBody>

                    </Cell>
                </Cells>
                <CellsTitle>推荐周期</CellsTitle>
                <Cells>
                    {this.state.items}
                </Cells>
                <ButtonArea>
                    <Button onClick={this.submit.bind(this)}>确定</Button>
                </ButtonArea>
            </div>
        );
    }
};
