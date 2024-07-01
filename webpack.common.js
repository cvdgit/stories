const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
  entry: {
    quiz: path.resolve(__dirname, 'asset/quiz/quiz.js'),
    audio: path.resolve(__dirname, 'asset/question/audio.js'),
    app: path.resolve(__dirname, 'asset/school/app.js'),
    course: path.resolve(__dirname, 'asset/course/app.js'),
    story_quiz: path.resolve(__dirname, 'asset/story-quiz/js/wikids-story-test.js'),
    slides: path.resolve(__dirname, 'asset/slides/js/slides.js'),
    wikids_gpt: path.resolve(__dirname, 'asset/gpt/index.js'),
    main: path.resolve(__dirname, 'asset/app/app.js'),
    pdf_chat: path.resolve(__dirname, 'asset/pdf_chat/index.js'),
    mental_map: path.resolve(__dirname, 'asset/mental_map/index.js'),
    mental_map_quiz: path.resolve(__dirname, 'asset/mental_map_quiz/index.js'),
  },
  output: {
    filename: '[name].js',
    path: path.resolve(__dirname, 'public_html/build'),
  },
  externals: {
    jquery: 'jQuery',
  },
  plugins: [
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
            "plugins": ["@babel/syntax-dynamic-import"],
            "presets": [
              [
                "@babel/preset-env",
                {
                  "targets": {
                    "node": "10.0.0"
                  }
                }
              ],
              "@babel/preset-react"
            ]
          }
        }
      },
      {
        test: /\.css$/,
        use: [MiniCssExtractPlugin.loader, 'css-loader'],
      },
      {
        test: /\.png$/,
        use: [
          {
            loader: "url-loader",
            options: {
              mimetype: "image/png",
            },
          },
        ],
      },
    ],
  }
};
