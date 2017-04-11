# Wordpress integration with Contently

### Versioning

The plugin follows _in spirit_ the conventions of [semantic versioning](http://semver.org/).

Given a version number MAJOR.MINOR.PATCH, increment the:

- MAJOR version when Contently's Stories API version changes
- MINOR version when there is a significant change (UI, etc) that attempts to be backwards compatible
- PATCH version when you make backwards-compatible bug fixes


### Packaging
1. Update the version number in ```index.php```
2. Run ```./bin/package```
3. Upload the new package to AWS S3 in ```integrations.contently.com:wordpress/releases```
  - _Be sure to change the permissions of the file so it's publicly accessible to read (but not write)_
4. Update the ```download_url``` and ```version``` in ```integrations.contently.com:wordpress/release.json```
5. Replace ```integrations.contently.com:wordpress/contently.zip``` with the new package to ensure that is up-to-date
5. Add the release to the Github repo
