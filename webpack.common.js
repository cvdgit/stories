const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin')

module.exports = {
    entry: {
      quiz: path.resolve(__dirname, 'asset/quiz/quiz.js'),
      audio: path.resolve(__dirname, 'asset/question/audio.js'),
      app: path.resolve(__dirname, 'asset/school/app.js'),
      course: path.resolve(__dirname, 'asset/course/app.js'),
      story_quiz: path.resolve(__dirname, 'asset/story-quiz/js/wikids-story-test.js')
    },
    output: {
        filename: '[name].js',
        path: path.resolve(__dirname, 'public_html/build'),
    },
    externals: {
        jquery: 'jQuery',
    },
    plugins: [
        //new CleanWebpackPlugin({
        //    exclude: /\.gitignore/,
        //}),
        new MiniCssExtractPlugin()
    ],
    module: {
        rules: [
            {
                test: /\.s[ac]ss$/i,
                use: [
                    MiniCssExtractPlugin.loader,
                    "css-loader",
                    "sass-loader",
                ],
            },
            {
                test: /\.m?js$/,
                exclude: /(node_modules|bower_components)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env']
                    }
                }
            },
            {
                test: /\.css$/,
                use: [MiniCssExtractPlugin.loader, 'css-loader'],
            },
        ],
    }
};
