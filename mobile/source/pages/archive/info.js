/**
 * Created by jf on 15/12/10.
 */

"use strict";

import WeUI from 'react-weui';
import "./archive.less";

const {Cells, Cell, CellHeader,CellsTitle, CellBody, CellFooter,Toast,Dialog}  = WeUI;
import Page from '../../component/page';
import Controller from '../../component/controller'
const {Alert} = Dialog;
import Time0 from '../user/images/time0.png';
import Time1 from '../user/images/time1.png';
import Mileage0 from '../user/images/mileage0.png';
import Mileage1 from '../user/images/mileage1.png';

export default class ArchiveInfo extends Controller {
    /**
     * 查询数据
     */
    componentWillMount() {
        super.componentWillMount();
        this.state.info = {
            issuing_authority: {},
            car_type: {},
            level: {},
            owner: {},
            port_type: {},
            traffic_card: {},
            maintain: {},
            safety_detection: {
                detect_company: {}
            },
            second_maintenance: {}
        };

        //  查询数据
        var url = '/data/car_info';
        if (this.props.routeParams.id) {
            url += "/" + this.props.routeParams.id
        }

        this.showLoading();
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                if (response.success == true) {
                    if (!response.result.safety_detection.detect_company) {
                        response.result.safety_detection.detect_company = {};
                    }
                    if (!response.result.mileage) {
                        response.result.mileage = 0;
                    }
                    if (!response.result.next_mileage) {
                        response.result.next_mileage = 0;
                    }
                    this.setState({info: response.result});
                } else {
                    this.showAlert(response.message);
                }
                this.stopLoading();
            }.bind(this)
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
                <Cells className="car_head">
                    <Cell className="basic">
                        <CellBody>
                            <div className="name"> {this.state.info.plate}({this.state.info.plate_color_name})
                                <div style={{float:"right"}}>{this.state.info.status}</div>
                            </div>
                            <div></div>
                            <br/>
                             <span className="info">
                             {this.state.info.economy_range}<br/>
                                 {this.state.info.car_type.name}
                                 | {this.state.info.checkton}吨 |长 {this.state.info.length} 宽 {this.state.info.width}
                                 高 {this.state.info.height}</span>
                        </CellBody>
                    </Cell>
                    <Cell>
                        <CellHeader className="big_header">
                            运输证号
                        </CellHeader>
                        <CellBody>
                            {this.state.info.traffic_card.number}
                        </CellBody>
                    </Cell>
                    <Cell>
                        <CellHeader className="big_header">
                            发证机关
                        </CellHeader>
                        <CellBody>
                            {this.state.info.issuing_authority.name}
                        </CellBody>
                    </Cell>
                    <Cell>
                        <CellHeader className="big_header">
                            发证时间
                        </CellHeader>
                        <CellBody>
                            {this.state.info.traffic_card.begin_date}
                        </CellBody>
                    </Cell>
                </Cells>
                <Cells access>


                    <Cell href="#/archive/photo" className="info_head">
                        <CellBody>
                            安全例检情况
                        </CellBody>
                        <CellFooter className="weui_cell_ft info_link">
                            图片
                        </CellFooter>
                    </Cell>
                    <Cell style={{display: this.state.info.safety_detection.detect_date? 'none': ''}}>
                        <CellBody>
                            该车辆无需例检，或者检测数据不存在！
                        </CellBody>
                    </Cell>

                    <div style={{display: this.state.info.safety_detection.detect_date? '':'none'}}>
                        <Cell>
                            <CellHeader className="big_header">时间</CellHeader>
                            <CellBody>
                                {this.state.info.safety_detection.detect_date}
                            </CellBody>
                        </Cell>
                        <Cell>
                            <CellHeader className="big_header">地点</CellHeader>
                            <CellBody>
                                {this.state.info.safety_detection.detect_company.name}
                            </CellBody>
                        </Cell>
                        <Cell>
                            <CellHeader className="big_header">结果</CellHeader>
                            <CellBody style={{color:"green"}}>
                                {this.state.info.safety_detection.resultName}
                            </CellBody>
                        </Cell>
                    </div>
                </Cells>
                <Cells access>
                    <Cell href={"#/maintain/list/"+this.state.info.key_no} className="info_head">
                        <CellBody >
                            二级维护情况
                        </CellBody>
                        <CellFooter className="weui_cell_ft info_link">
                            历史记录
                        </CellFooter>
                    </Cell>
                    <Cell>
                        <CellHeader className="big_header">最近二维</CellHeader>
                        <CellBody>
                            {this.state.info.second_maintenance.in_factory_date}
                            <br/>里程读数{this.state.info.mileage}千米
                        </CellBody>
                    </Cell>
                    <Cell>
                        <CellHeader className="big_header">下次二维</CellHeader>
                        <CellBody style={{color:"black"}}>
                            {this.state.info.next_second_detect_date}
                            <br/>里程不超过{this.state.info.next_mileage}千米
                        </CellBody>
                    </Cell>

                    <Cell href={'#/user/strategy/' + this.state.info.key_no}>
                        <CellHeader className="big_header"> 维护计划</CellHeader>
                        <CellBody>
                            <span style={{display:this.state.info.second_maintenance.time_cycle?"":"none"}}>
                                <img src={Time1}
                                     style={{width:'1em'}}/>&nbsp;{this.state.info.second_maintenance.time_cycle}日&nbsp;&nbsp;
                            </span>
                            <span style={{display:this.state.info.second_maintenance.mileage_cycle?"":"none"}}>

                                <img
                                    style={{width:'1em'}}
                                    src={Mileage1}/>&nbsp;{this.state.info.second_maintenance.mileage_cycle}千米
                            </span>
                        </CellBody>
                        <CellFooter>
                            修改
                        </CellFooter>
                    </Cell>
                </Cells>
                <Cells >
                    <Cell className="info_head">
                        <CellBody>
                            技术等级评定及年审
                        </CellBody>
                    </Cell>
                    <Cell>
                        <CellHeader className="big_header">等级评定日</CellHeader>
                        <CellBody>
                            {this.state.info.level.assess_date}
                        </CellBody>
                    </Cell>
                    <Cell>
                        <CellHeader className="big_header">下次检测日</CellHeader>
                        <CellBody style={{color:"black"}}>
                            {this.state.info.next_detect_date}
                        </CellBody>
                    </Cell>

                    <Cell>
                        <CellHeader className="big_header">技术等级</CellHeader>
                        <CellBody>
                            {this.state.info.level.name}
                        </CellBody>
                    </Cell>
                    <Cell>
                        <CellHeader className="big_header">年审日期</CellHeader>
                        <CellBody style={{color:"black"}}>
                            {this.state.info.check_date}
                        </CellBody>
                    </Cell>
                </Cells>
            </div>
        );
    }
};
