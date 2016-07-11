import './index.less';
import Controller from '../../component/controller'

/**
 * 服务公司地图界面
 */
export default class ServiceMap extends Controller {
    /**
     * 加载前
     */
    componentWillMount() {
        super.componentWillMount();
        this.state.width = document.documentElement.clientWidth;
        this.state.height = document.documentElement.clientHeight;
        this.setState({'height': this.state.height});
        console.log(' this.state.height', this.state.height);
    }

    /**
     * 被加载后
     */
    componentDidMount() {
        // 地址
        wx.ready(
            function () {
                this.syncLocation();

            }.bind(this)
        )
    }


    /**
     * 同步当前位置
     */
    syncLocation() {
        wx.getLocation({
            type: 'gcj02',
            success: function (res) {
                console.log('syncLocation');
                //        var res = {latitude: 24.480601, longitude: 118.172301};

                //  创建地图
                var map = this.createMap(res.latitude, res.longitude);

                //中心坐标
                var center = new qq.maps.LatLng(res.latitude, res.longitude);
                var anchor = new qq.maps.Point(6, 6),
                    size = new qq.maps.Size(24, 24),
                    origin = new qq.maps.Point(0, 0),
                    icon = new qq.maps.MarkerImage('http://lbs.qq.com/javascript_v2/img/center.gif', size, origin, anchor);
                new qq.maps.Marker({
                    icon: icon,
                    map: map,
                    position: center
                });

                // 创建地点
                $.ajax({
                    url: './index.php?s=/addon/School/place/listAllPlace',
                    type: 'GET',
                    success: function (response) {
                        var num = 1;
                        var tokens = {};
                        for (var item in response) {
                            //console.log('item', item);
                            if (!tokens[response[item]['token']]) {
                                tokens[response[item]['token']] = num;
                                num++;
                            }

                            this.createMarker(response[item], tokens[response[item]['token']]);

                        }
                        this.stopLoading();
                    }.bind(this)
                });
            }.bind(this)
        });
    }

    /**
     * 创建地图
     */
    createMap(latitude, longitude) {
        //中心
        var center = new qq.maps.LatLng(latitude, longitude);

        //创建地图
        var map = new qq.maps.Map(document.getElementById('map'), {
            center: center,
            zoom: 13
        });


        //配置地图
        this.state.map = map;

        qq.maps.event.addListener(map, 'click', function () {
            if (this.state.popInfo) {
                this.state.popInfo.close();
            }
        }.bind(this));

        return map;
    }

    /**
     * 创建标签
     */
    createMarker(item, num) {
        // 判断是否有坐标
        if (!item.coordinate) {
            return;
        }

        // 中心
        var coordinates = item.coordinate.split(',');
        console.log('coordinates', coordinates);
        var center = new qq.maps.LatLng(coordinates[1], coordinates[0]);

        //中心坐标
        var anchor = new qq.maps.Point(12, 0),
            size = new qq.maps.Size(24, 24),
            origin = new qq.maps.Point(0, 0),
            icon = new qq.maps.MarkerImage('./mobile/img/place.png', size, origin, anchor, size);
        var marker = new qq.maps.Marker({
            icon: icon,
            map: this.state.map,
            position: center
        });

        // 添加到提示窗
        var info = new qq.maps.InfoWindow({
            map: this.state.map
        });

        // 获取标记的点击事件
        qq.maps.event.addListener(marker, 'click', function () {
            if (this.state.popInfo) {
                this.state.popInfo.close();
            }
            info.open();
            info.setContent('<div style="text-align:center;white-space:nowrap;' +
                'margin:10px;">' + item.school_name + item.name + '&nbsp;<a href="#/service/info/' + item.token + '">报名</a></div>');
            info.setPosition(center);
            this.state.popInfo = info;
        }.bind(this));


    }


    /**
     * 渲染
     * @returns {XML}
     */
    render() {
        return (
            <div style={{width:this.state.width, height : this.state.height}} className="page">
                <div id="map"></div>
            </div>
        );
    }
};
