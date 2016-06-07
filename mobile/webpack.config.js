/**
 * Created by jf on 15/10/27.
 */
var webpack = require('webpack');
var path = require('path');


module.exports = {
    entry: {
        "app": [
            path.resolve(__dirname, "./source/app.js")
        ],
        //第三方库
        "lib": ['jquery', 'react', 'react-dom']
    },
    externals: {
        "react": "React",
        "jquery": "jQuery",
        "react-dom": "ReactDOM"

    },
    output: {
        path: path.resolve(__dirname, '..'),
        publicPath: '',
        filename:  "./mobile/javascript/[name].js"
    },
    module: {
        loaders: [
            {
                test: /\.js[x]?$/,
                exclude: /node_modules/,
                loader: 'babel'
            }, {
                test: /\.less$/,
                loader: 'style!css!autoprefixer!less'
            }, {
                test: /\.css/,
                loader: 'style!css'
            }, {
                test: /\.(png|jpg)$/,
                loader: 'url?limit=25000'
            },
            {test: /\.(eot|ttf|svg|woff)$/, loader: "file-loader?name=mobile/file/[hash].[ext]"}
        ]
    },
    plugins: [
        new webpack.optimize.CommonsChunkPlugin("lib", "./mobile/javascript/lib.js", Infinity)
        , new webpack.HotModuleReplacementPlugin()
    ]
};
