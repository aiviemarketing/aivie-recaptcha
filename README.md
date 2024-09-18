# Aivie Mautic reCAPTCHA Plugin

[![license](https://img.shields.io/packagist/v/koco/mautic-recaptcha-bundle.svg)](https://packagist.org/packages/koco/mautic-recaptcha-bundle) 
[![Packagist](https://img.shields.io/packagist/l/koco/mautic-recaptcha-bundle.svg)](LICENSE)
[![mautic](https://img.shields.io/badge/mautic-3%20&%204-blue.svg)](https://www.mautic.org/mixin/recaptcha/)

This Plugin brings reCAPTCHA integration to mautic 3 and 4.

## Installation

### mautic 3 & 4
Supports reCaptcha v3.

Add this code to the plugins/ directory of your mautic installation.

Execute `composer require google/cloud-recaptcha-enterprise` in the main directory of the mautic installation.

> @todo needs to be moved to the plugins composer.json, when we support composer v2. So we can install the https://github.com/mautic/composer-plugin.


## Configuration
Navigate to the Plugins page and click "Install/Upgrade Plugins". You should now see a "reCAPTCHA" plugin. Open it to enable it.

Set the following ENV variables on your server:
- GOOGLE_CLOUD_PROJECT
- GC_RECAPTCHA_SITE_KEY
- GOOGLE_APPLICATION_CREDENTIALS

The plugin relies on [Google reCAPTCHA Enterprise](https://cloud.google.com/recaptcha). For it to work it needs [Google Application Default Credentials](https://cloud.google.com/docs/authentication/application-default-credentials#GAC). 

## Usage in a Mautic Form
Add "reCAPTCHA" field to the Form and save changes.
![mautic form](/doc/form_preview.png?raw=true "Mautic Form with reCAPTCHA")

## Score validation

reCAPTCHA v3 will rank traffic and interactions based on a score of 0.0 to 1.0, with a 1.0 being a good interaction and scores closer to 0.0 indicating a good likelihood that the traffic was generated by bots

![score validation](/doc/score-validation.png?raw=true "plugin config")

## Based on 
https://github.com/KonstantinCodes/mautic-recaptcha