import {SimilarityChecker} from "./calcSimilarity";
import sendMessage from "./sendMessage";
import {stripTags} from "../common";

function checkDiffSimilarity(text, userResponse, threshold) {
  const similarityChecker = new SimilarityChecker(threshold);
  const responseIsSuccess = similarityChecker.check(text, userResponse);
  if (!responseIsSuccess) {
    return null;
  }
  return JSON.stringify({
    "similarity_percentage": similarityChecker.getSimilarityPercentage(),
    "all_important_words_included": true,
    "user_response": userResponse
  });
}

async function rewriteUserResponse(text, userResponse) {
  let rewriteUserResponse = '';
  await sendMessage(
    `/admin/index.php?r=gpt/stream/retelling-rewrite`,
    {
      userResponse,
      slideTexts: stripTags(text)
    },
    (message) => rewriteUserResponse = message,
    () => console.log('rewrite error'),
    () => console.log('rewrite end')
  )
  return rewriteUserResponse.trim();
}

async function retellingTreeChecker(text, userResponse, promptId) {
  let response = '';
  const remPunctuation = text => text.replace(/[!"#$%&'()*+,./:;<=>?@[\]^`{|}«»~]/g, '').replace(/\s{2,}/g, " ");
  await sendMessage(`/admin/index.php?r=gpt/stream/retelling-tree`, {
      userResponse,
      slideTexts: stripTags(text).replaceAll('_', ' '),
      importantWords: $(`<div>${text}</div>`)
        .find('span.target-text')
        .map((i, el) => remPunctuation($(el).text()).replaceAll('_', ' '))
        .get()
        .join(', '),
      promptId
    },
    (message) => response = message,
    (error) => console.error(error),
    () => {
    }
  );
  return response;
}

export async function userResponseChecker(text, userResponse, threshold, promptId) {

  let result = checkDiffSimilarity(text, userResponse, threshold);
  if (result !== null) {
    return result;
  }

  const userResponseRewrite = await rewriteUserResponse(text, userResponse);

  result = checkDiffSimilarity(text, userResponseRewrite, threshold);
  if (result !== null) {
    return result;
  }

  return await retellingTreeChecker(
    text,
    userResponseRewrite,
    promptId
  );
}
