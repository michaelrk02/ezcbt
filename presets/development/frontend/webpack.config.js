const path = require('path');

module.exports = {
    optimization: {
        splitChunks: {
            chunks: 'all',
            automaticNameDelimiter: '-'
        }
    },
    entry: {
        styles: './styles.js',
        user: './user.js'
    },
    output: {
        filename: '[name].app.js',
        path: path.resolve(__dirname, '../public/')
    },
    mode: 'development', // TODO
    devtool: 'inline-source-map', // TODO
    module: {
        rules: [
            {
                test: /\.js$/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env'],
                        cacheDirectory: true
                    }
                }
            },
            {
                test: /\.s[ac]ss$/,
                exclude: /node_modules/,
                use: ['style-loader', 'css-loader', 'sass-loader']
            }
        ]
    }
};
