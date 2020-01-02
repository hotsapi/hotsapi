const {expect} = require('chai');
const { httpGet } = require('./test_helper');

async function performWebTest(url) {
  const response = await httpGet('/');
  expect(response.status).to.equal(200);
  expect(response.data).to.not.be.empty;
}

const links = [
  '/',
  '/upload',
  '/docs',
  '/swagger',
  '/spec/hotsapi-1.0.yaml?1',
  '/faq',
];

describe('Web', () => {
  for (const link of links) {
    it(link, async () => {
      await performWebTest(link);
    });
  }
});
