/**
 * Created by wuhanchu on 16/3/5.
 */
"use strict";

var React = require('react');
export default class Controller extends React.Component {
    /**
     * 查询数据
     */
    componentWillMount() {
        this.state = {
            alert: {
                title: '错误',
                message: "发生错误!",
                buttons: [
                    {
                        label: '好的',
                        onClick: this.hideAlert.bind(this)
                    }
                ]
            },
            confirm: {
                title: '请确认',
            },
            showLoading: false
        }
    }

    /**
     * 取得路径参数
     * @param name
     * @returns {*}
     */
    getQueryString(name) {
        var search = window.location.href.split("?")[1];
        if (!search) {
            return null;
        }
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var result = search.match(reg);
        if (result != null) {
            return result[2];
        } else {
            return null;
        }
        ;
    }

    /**
     * ajax 错误信息处理
     * @param jqXHR
     * @param textStatus
     * @param errorThrown
     */
    error_handle(jqXHR, textStatus, errorThrown) {
        if (jqXHR.responseJSON) {
            this.showAlert(jqXHR.responseJSON.message);
        }else if (jqXHR.responseJSON.code == 'DH002') {
            this.state.showAlert = true;
            this.state.alert.message = response.message;
            this.state.alert.title = '提示信息';
            this.setState(this.state);
        }
        else {
            this.showAlert(jqXHR.statusText);
        }
        this.stopLoading();
    }

    /**
     * 显示警告
     */
    showAlert(message) {
        this.state.showAlert = true;

        this.state.alert.message = message;
        if (!this.state.alert.message) {
            this.state.alert.message = '系统出错';
        }
        this.setState(this.state);
    }

    /**
     * 隐藏警告
     */
    hideAlert() {
        this.setState({showAlert: false});
    }

    /**
     * 显示确认信息
     * @param message 信息
     * @param confirmBack 确认返回调用
     * @param cancelBack 取消返回调用
     */
    showConfirm(message, confirmBack, cancelBack) {
        this.state.showConfirm = true;
        this.state.confirm = {
            message: message,
            buttons: [
                {
                    type: 'warn',
                    label: '取消',
                    onClick: function () {
                        if (cancelBack) cancelBack();
                        this.setState({showConfirm: false});
                    }.bind(this)

                },
                {
                    type: 'primary',
                    label: '确认',
                    onClick: function () {
                        if (confirmBack) confirmBack();
                        this.setState({showConfirm: false});
                    }.bind(this)
                }
            ]
        }

        this.setState(this.state);
    }


    /**
     * 显示加载
     */
    showLoading() {
        this.setState({showLoading: true});
    }

    /**
     * 隐藏加载
     */
    stopLoading() {
        this.setState({showLoading: false});
    }

    /**
     * 隐藏警告
     */
    hideConfirm() {
        this.state.showConfirm = false;
        this.setState(this.state);
    }

    /**
     * 输入框修改
     * @param event
     */
    valueChange(event) {
        this.state.info[event.target.name] = event.target.value;
        this.setState(this.state);
    }
}
