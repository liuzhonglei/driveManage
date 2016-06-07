/**
 * Created by jf on 15/12/10.
 */

"use strict";

import WeUI from 'react-weui';


import Page from '../../component/page';
import Controller from '../../component/controller'

const PHOTO_SET = [
    {
        src: 'https://farm8.staticflickr.com/7263/8168484671_f47b9e53bf_c.jpg',
        width: 681,
        height: 1024,
        aspectRatio: 1.5,
        lightboxImage: {
            src: 'https://farm9.staticflickr.com/8337/8168490235_0b0dcb1a7d.jpg',
            srcset: [
                'https://farm9.staticflickr.com/8337/8168490235_0b0dcb1a7d.jpg 1024w',
                'https://farm9.staticflickr.com/8337/8168490235_0b0dcb1a7d.jpg 800w',
                'https://farm9.staticflickr.com/8337/8168490235_0b0dcb1a7d.jpg 500w',
                'https://farm9.staticflickr.com/8337/8168490235_0b0dcb1a7d.jpg 320w',
            ]
        }
    }
];

export default class MaintainPhoto extends Controller {


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
        this.state.show = false;
    }


    /**
     * 渲染
     * @returns {XML}
     */
    render() {
        return (
            <div className="page gallery">
                <div className="title">
                    <h1>安全例检记录</h1>
                    <h2>跟踪维护中的所有记录</h2>
                </div>
                <div className="gallery_div">
                    <img src="https://farm9.staticflickr.com/8337/8168490235_0b0dcb1a7d.jpg"/>
                </div>
                <div className="gallery_div">
                    <img className="gallery_photo" src="https://farm9.staticflickr.com/8337/8168490235_0b0dcb1a7d.jpg"/>
                </div>
            </div>
        );
    }
};
