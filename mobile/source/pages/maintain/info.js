/**
 * Created by jf on 15/12/10.
 */

"use strict";

import WeUI from 'react-weui'

const {Cells, Cell, CellHeader,CellsTitle, CellBody, CellFooter,Dialog,Toast}  = WeUI;
import Page from '../../component/page';
import Controller from '../../component/controller'
const {Alert,Confirm} = Dialog;

import  'raty-js'
import '../../../node_modules/raty-js/lib/jquery.raty.css';

/**
 * 二维信息对象
 */
export default class MaintainInfo extends Controller {

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
            authCode: null,
            check_corp: {},
            ext_content: {},
            check_main: {},
            entry_info: {}
        }

        // 加载列表
        this.showLoading();
        $.ajax({
            url: '/data/common',
            type: 'POST',
            data: {
                source: 'maintain/second_maintenance/' + this.props.routeParams.id
            },
            success: function (response) {
                this.stopLoading();
                if (response.success == true) {
                    // 配置对应数据
                    if (!response.result.ext_content) {
                        response.result.ext_content = {};
                    }
                    if (!response.result.check_main) {
                        response.result.check_main = {};
                    }
                    if (!response.result.entry_info) {
                        response.result.entry_info = {};
                    }
                    this.setState({info: response.result});

                    // 设置评级
                    if (this.state.info.ext_content.grade) {
                        $("[name='grade']").raty({
                            starType: 'i',
                            number: 5,
                            score: parseInt(this.state.info.ext_content.grade),
                            readOnly: true
                        });
                    } else {
                        $("[name='grade']").raty({
                            starType: 'i', number: 5, score: 0, readOnly: false, click: function (grade) {
                                this.showConfirm('确认评级为' + grade + '颗星?', function () {
                                    this.maintain_grade(grade);
                                }.bind(this), function () {
                                    $("[name='grade']").raty('score', 0);
                                })
                            }.bind(this)
                        });
                    }
                } else {
                    this.showAlert(response.message);
                }
            }.bind(this),
            error: function (jqXHR, textStatus, errorThrown) {
                this.stopLoading();
                if (jqXHR.responseJSON) {
                    this.showAlert(jqXHR.responseJSON.message);
                } else {
                    this.showAlert(jqXHR.statusText);

                }
            }.bind(this)
        });
    }

    /**
     * 进行评级操作
     */
    maintain_grade(grade) {
        this.showLoading();
        $.ajax({
            url: '/data/common',
            type: 'POST',
            data: {
                source: 'maintain/second_maintenance/' + this.props.routeParams.id + '/grade',
                grade: grade,
                method: "POST"
            },
            success: function (response) {
                this.stopLoading();
                if (response.success == true) {
                    this.state.info.ext_content = response.result;
                    this.state.showToast = true;
                    this.setState({info: this.state.info});
                    this.state.toastTimer = setTimeout(()=> {
                            this.setState({showToast: false});
                        }
                        , 2000
                    )
                } else {
                    this.showAlert(response.message);
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


    /**a
     * 渲染
     * @returns {XML}
     */
    render() {
        return (
            <Page title="维护明细">
                <Toast show={this.state.showToast}>评级成功</Toast>
                <Toast icon="loading" show={this.state.showLoading}>正在加载中...</Toast>
                <Alert title={this.state.alert.title} buttons={this.state.alert.buttons} show={this.state.showAlert}>
                    {this.state.alert.message}
                </Alert>
                <Confirm title={this.state.confirm.title} buttons={this.state.confirm.buttons}
                         show={this.state.showConfirm}>
                    {this.state.confirm.message}
                </Confirm>
                <Cells access>
                    <Cell>
                        <CellHeader className="half_header">出厂合格证</CellHeader>
                        <CellBody>
                            {this.state.info.bill_no}
                        </CellBody>
                    </Cell>
                    <Cell>
                        <CellHeader className="half_header">报备日期</CellHeader>
                        <CellBody>
                            {this.state.info.entry_info.upload_date}
                        </CellBody>
                    </Cell>
                    <Cell>
                        <CellHeader className="half_header">进厂时间</CellHeader>
                        <CellBody>
                            {this.state.info.entry_info.start_date ? this.state.info.entry_info.start_date + " " + this.state.info.entry_info.start_time : ''}
                        </CellBody>
                    </Cell>
                    <Cell>
                        <CellHeader className="half_header">完工时间</CellHeader>
                        <CellBody>
                            {this.state.info.send_date}
                        </CellBody>
                    </Cell>
                    <Cell>
                        <CellHeader className="half_header"> 维护耗时
                        </CellHeader>
                        <CellBody>
                            {this.state.info.handle_time}
                        </CellBody>
                    </Cell>
                    <Cell>
                        <CellHeader className="half_header">检查员</CellHeader>
                        <CellBody>
                            {this.state.info.checker}
                        </CellBody>
                    </Cell>
                    <Cell href={'#/service/info/'+this.state.info.check_corp_code}>
                        <CellHeader className="half_header">维护企业</CellHeader>
                        <CellBody>
                            {this.state.info.check_corp.name}
                        </CellBody>
                        <CellFooter>
                            详情
                        </CellFooter>
                    </Cell>

                    <Cell href={"#/maintain/photo/"+this.props.routeParams.id}>
                        <CellBody>
                            相关图片
                        </CellBody>
                        <CellFooter>
                            查看
                        </CellFooter>
                    </Cell>
                </Cells>

                <CellsTitle>我的评级{this.state.info.ext_content.grade ? '' : '(未评级)'}</CellsTitle>
                <Cells access>
                    <Cell>
                        <CellBody>
                            <div name="grade" className="grade"></div>
                        </CellBody>
                    </Cell>
                </Cells>
                <Cells access>
                    <Cell href={"#/maintain/complaint/"+this.props.routeParams.id}>
                        <CellBody>
                            我要投诉
                        </CellBody>
                        <CellFooter>
                        </CellFooter>
                    </Cell>
                </Cells>
            </Page>
        );
    }
};
