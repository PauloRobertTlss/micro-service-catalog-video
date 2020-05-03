Recreate image
-- docker-compose build --no-cache php web                                                                                                                                                                              ─╯

--encrypt GCP file.

─ gcloud kms encrypt --ciphertext-file=./storage/credentials/gcp/codeeductiontests-92f7b42b6c8a.enc --plaintext-file=./storage/credentials/gcp/codeeductiontests-92f7b42b6c8a.json --location=global --keyring=7ebb6041-2b18-478e-a2c6-8196f34b50d8 --key=service_account

