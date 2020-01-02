const chai = require('chai');
const chaiAsPromised = require('chai-as-promised');
const {expect} = require('chai');
const { performTest } = require('./test_helper');

chai.use(chaiAsPromised);

describe('Api', () => {
  describe('/replays', () => {
    it('/', async () => {
      await performTest('/replays');
    });

    it('/parsed', async () => {
      await performTest('/replays/parsed');
    });

    it('/1', async () => {
      await performTest('/replays/1');
    });

    it('/2', async () => {
      await performTest('/replays/2');
    });

    it('/3', async () => {
      await expect(performTest('/replays/3')).to.be.rejectedWith('Request failed with status code 404')
    });

    it('/fingerprints/v3/056dc095-b206-52dd-f7b3-18760e64db00', async () => {
      await performTest('/replays/fingerprints/v3/056dc095-b206-52dd-f7b3-18760e64db00');
    });


    it('/fingerprints/v3/57ed6692-cf0d-58de-1709-ea90fed96a30', async () => {
      await performTest('/replays/fingerprints/v3/57ed6692-cf0d-58de-1709-ea90fed96a30');
    });

    it('/fingerprints/v3/74e29c2e-47da-4fca-a358-80ae8c9c3e01', async () => {
      await performTest('/replays/fingerprints/v3/74e29c2e-47da-4fca-a358-80ae8c9c3e01');
    });

    it('/min-build', async () => {
      await performTest('/replays/min-build');
    });

    it('/fingerprints', async () => {
      const fingerprints = "056dc095-b206-52dd-f7b3-18760e64db00\n74e29c2e-47da-4fca-a358-80ae8c9c3e01";
      await performTest('/replays/fingerprints', fingerprints);
    });
  });

  describe('/talents', () => {
    it('/', async () => {
      await performTest('/talents');
    });

    it('/talents/WitchDoctorPandemic', async () => {
      await performTest('/talents/WitchDoctorPandemic');
    });

    it('/talents/ShouldError', async () => {
      await expect(performTest('/talents/ShouldError')).to.be.rejectedWith('Request failed with status code 404')
    });
  })

  describe('/heroes', () => {
    it('/', async () => {
      await performTest('/heroes');
    });

    it('/valeera', async () => {
      await performTest('/heroes/valeera');
    });

    it('/johanna/abilities/Q1', async () => {
      await performTest('/heroes/johanna/abilities/Q1');
    });
  })
});
