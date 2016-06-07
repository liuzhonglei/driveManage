/**
 * Created by jf on 15/12/10.
 */

"use strict";

import WeUI from 'react-weui'
import FontAwesome from 'react-fontawesome';

const {Cells, Cell, CellHeader,CellsTitle, CellBody, CellFooter,Button,Toast, Dialog}  = WeUI;
import Page from '../../component/page';
import Controller from '../../component/controller'
import './archive.less';
const {Alert} = Dialog;


export default class ArchiveList extends Controller {

    /**
     * 查询数据
     */
    componentWillMount() {
        super.componentWillMount();
        this.state.info = {
            name: null,
            carNumber: null,
            transportNumber: null,
            phone: null,
            authCode: null
        }
        //this.state.cycles = {};
        this.state.items = [];

        // 查询数据
        this.showLoading();
        $.ajax({
            url: '/data/car_info/List',
            type: 'GET',
            success: function (response) {
                if (response.success == true) {
                    this.state.list = response.result;
                    //this.state.cycles = response.cycles;
                    this.setState(this.state);
                    console.log('this.state', this.state);
                    this.refreshList();
                    //for (var i = 0; i < response.result.length; i++) {
                    //    if (response.result[i].owner && response.result[i].owner.code) {
                    //        this.refreshStatus(response.result[i].owner.code);
                    //        break;
                    //    }
                    //}
                } else {
                    this.showAlert(response.message);
                }
                this.stopLoading();
            }.bind(this)
        });
    }

    ///**
    // * 更新车的状态
    // * @param code 车的代码
    // */
    //refreshStatus(code) {
    //    this.showLoading();
    //    $.ajax({
    //        url: '/data/common',
    //        data: {
    //            source: 'maintain/second_maintenance_cycles/list',
    //            company_code: code
    //        },
    //        type: 'POST',
    //        success: function (response) {
    //            if (response.success == true) {
    //                response.result.forEach(function (entry) {
    //                    this.state.cycles[entry.company_code] = true;
    //                })
    //                console.log('this.state.cycles', this.state.cycles);
    //                this.refreshList();
    //            } else {
    //                this.showAlert(response.message);
    //            }
    //            this.stopLoading();
    //        }.bind(this)
    //    });
    //
    //}

    /**
     * 跳转二维周期配置界面
     * @param plcate 车牌号码
     * @param plate_color 车牌颜色
     */
    maintainConf(event, id) {
        event.stopPropagation();
        event.cancelBubble = true;
        event.preventDefault();
        event.stopImmediatePropagation();
        window.location.href = '#/user/strategy/' + id;
    }

    archiveInfo(event, id) {
        if (!event.cancelBubble)
            window.location.href = '#/archive/info/' + id;
    }

    //更新列表
    refreshList() {
        this.state.items = [];
        for (var i = 0; i < this.state.list.length; i++) {
            this.state.items.push(
                <Cell onClick={this.archiveInfo.bind(this,event,this.state.list[i].key_no)}>
                    <CellBody className="archive-list">
                        <p className="car-info">
                            <span
                                className="title">{this.state.list[i].plate + this.state.list[i].plate_color_name}</span>
                            <span className="end">{this.state.list[i].status}</span>
                        </p>

                        <p className="maintain-info">

                            <span
                                style={{color:"red",display: this.state.list[i].second_maintenance.time_cycle || this.state.list[i].second_maintenance.mileage_cycle ? 'none':''}}
                                > <FontAwesome name='cog'
                                               size="lg"/> &nbsp;二级维护周期未设定<br/></span>


                            <div style={{display: this.state.list[i].next_second_detect_date?'':'none'}}>
                                下次二维日期: {this.state.list[i].next_second_detect_date}
                            </div>
                            <div style={{display: this.state.list[i].next_mileage?'':'none'}}>
                                下次二维公里:{parseInt(this.state.list[i].next_mileage)}千米
                            </div>
                        </p>

                        <p className='button'>
                            <span className="end">
                                <Button
                                    onClick={this.maintainConf.bind(this,event,this.state.list[i].key_no)}
                                    style={{alignText:'right'}} type="default" size="small">设置二维周期</Button>
                             </span>
                        </p>
                    </CellBody>
                </Cell>
            )
        }
    }

    /**
     * 渲染
     * @returns {XML}
     */
    render() {
        return (
            <Page title="车辆管理">
                <Toast icon="loading" show={this.state.showLoading}>正在加载中...</Toast>
                <Alert title={this.state.alert.title} buttons={this.state.alert.buttons}
                       show={this.state.showAlert}>
                    {this.state.alert.message}
                </Alert>
                <Cells access>
                    {this.state.items}
                </Cells>
            </Page >
        );
    }
};
