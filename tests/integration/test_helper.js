const axios = require('axios');
const chai = require('chai');
const chaiSubset = require('chai-subset');
const { promises: fs } = require('fs');
const path = require('path');

const expect = chai.expect;
chai.use(chaiSubset);

const baseUrl = 'http://localhost:8080';

function httpGet(url) {
  return axios.get(`${baseUrl}${url}`, { transformResponse: (res) => res });
}

async function apiGet(url) {
  const response = await httpGet(`/api/v1${url}`);
  return response.data;
}

async function retrieveExpectedResponse(filePath, data) {
  try {
    await fs.access(filePath);
    const fileData = await fs.readFile(filePath, 'utf-8');
    if (fileData.trim() !== '') {
      return JSON.parse(fileData);
    }
  } catch (err) {
    if (err.code !== 'ENOENT') {
      throw err;
    }
  }

  await fs.writeFile(filePath, data, 'utf-8');
  return JSON.parse(data);
}

function generateFilename(url) {
  const currentPath = path.resolve(__dirname);
  const filename =  url.toLowerCase().replace(/^\//g, '').replace(/\//g, '_');
  return path.join(currentPath, 'fixtures', filename + '.json');
}

async function performTest(url, requestBody) {
  const filePath = generateFilename(url);
  let response = '';
  if (requestBody) {
    response = await axios.post(`${baseUrl}/api/v1${url}`, requestBody, { transformResponse: (res) => res});
  } else {
    response = await httpGet(`/api/v1${url}`);
  }

  const expected = await retrieveExpectedResponse(filePath, response.data);

  expect(JSON.parse(response.data)).to.containSubset(expected);
}

module.exports = {
  httpGet,
  performTest,
};
