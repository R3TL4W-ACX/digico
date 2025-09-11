# Drupal Site Upgrade Action Plan

Based on the comprehensive assessment, here's your prioritized upgrade roadmap:

## Current State Summary
- **Drupal Version:** 8.5.3 (Very outdated, needs upgrade)
- **PHP Version:** 7.4.33 (✓ Compatible with D9, ✗ Needs upgrade for D10)
- **Content:** 8 pages, 10 paragraph types, custom theme
- **Modules:** 15 contrib + 5 custom modules
- **Overall Health:** Functional but requires significant updates

## Phase 1: Immediate Safety Updates (1-2 days)
**Risk Level:** LOW - These are safe updates with minimal breaking changes

### 1.1 Create Backup
```bash
# Database backup
ddev export-db --file=backup-before-updates.sql.gz

# Full site backup 
tar -czf site-backup-$(date +%Y%m%d).tar.gz .
```

### 1.2 Update Safe Modules
```bash
# Safe module updates (backward compatible)
ddev composer require 'drupal/colorbox:^1.6'
ddev composer require 'drupal/entity_reference_revisions:^1.10' 
ddev composer require 'drupal/fieldblock:^2.0'
ddev composer require 'drupal/captcha:^1.5'

# Clear cache and test
ddev drush cr
```

### 1.3 Test Functionality
- [ ] Homepage loads correctly
- [ ] All existing pages display properly
- [ ] Paragraph types work in admin
- [ ] Contact forms function
- [ ] No PHP errors in logs

## Phase 2: Core Drupal Update (3-5 days)
**Risk Level:** MEDIUM - Requires testing

### 2.1 Upgrade to Drupal 8.9.x (LTS)
```bash
# Update to latest D8 LTS first
ddev composer require 'drupal/core-recommended:^8.9' 'drupal/core-composer-scaffold:^8.9' 'drupal/core-project-message:^8.9'

# Run database updates
ddev drush updb -y
ddev drush cr
```

### 2.2 Verify All Functionality
- [ ] Admin interface works
- [ ] Content editing functions
- [ ] Theme displays correctly
- [ ] All modules enabled and working

## Phase 3: Major Module Updates (5-7 days)
**Risk Level:** HIGH - May have breaking changes

### 3.1 Update Major Modules (one at a time)
```bash
# Google Analytics (breaking changes expected)
ddev composer require 'drupal/google_analytics:^4.0'
ddev drush updb -y

# Context module (major version change)
ddev composer require 'drupal/context:^4.0'
ddev drush updb -y

# Editor Advanced Link (new major version)
ddev composer require 'drupal/editor_advanced_link:^2.0'
ddev drush updb -y
```

### 3.2 Reconfigure Updated Modules
- [ ] Google Analytics: Reconfigure tracking settings
- [ ] Context: Review and update context rules
- [ ] Editor Advanced Link: Test link functionality

## Phase 4: Remove Problematic Modules (1-2 days)
**Risk Level:** MEDIUM - Functionality may be lost

### 4.1 Assess Usage
```bash
# Check if these modules are actually used
ddev drush pmu ng_lightbox colorbox_load --simulate
```

### 4.2 Remove Abandoned Modules
```bash
# If not critical, remove
ddev drush pmu ng_lightbox colorbox_load -y
ddev composer remove drupal/ng_lightbox drupal/colorbox_load
```

### 4.3 Replace Functionality
- Use Colorbox for lightbox needs instead of ng_lightbox
- Remove colorbox_load if not needed

## Phase 5: Drupal 9 Upgrade (7-10 days)
**Risk Level:** HIGH - Major version upgrade

### 5.1 Pre-upgrade Checks
```bash
# Install upgrade status module
ddev composer require drupal/upgrade_status
ddev drush en upgrade_status -y

# Check D9 readiness
ddev drush upgrade_status:analyze
```

### 5.2 Theme Compatibility
- [ ] Review custom theme (digico2018) for D9 compatibility
- [ ] Update theme templates if needed
- [ ] Test theme functionality

### 5.3 Perform D9 Upgrade
```bash
# Update to Drupal 9
ddev composer require 'drupal/core-recommended:^9.5' 'drupal/core-composer-scaffold:^9.5' 'drupal/core-project-message:^9.5' --update-with-dependencies

# Run updates
ddev drush updb -y
ddev drush cr
```

## Phase 6: Final Optimization (2-3 days)
**Risk Level:** LOW

### 6.1 Performance Tuning
- [ ] Enable CSS/JS aggregation (already enabled)
- [ ] Configure caching (set cache max age > 0)
- [ ] Optimize database

### 6.2 Security Hardening
- [ ] Update all modules to latest versions
- [ ] Review user permissions
- [ ] Enable security modules if needed

### 6.3 Final Testing
- [ ] Full site functionality test
- [ ] Performance testing
- [ ] Mobile responsiveness
- [ ] SEO elements (meta tags, etc.)

## Phase 7: Future Planning (Ongoing)

### 7.1 Drupal 10 Preparation
- [ ] Monitor PHP 8.1+ availability in hosting
- [ ] Plan D10 upgrade for next year
- [ ] Keep modules updated

### 7.2 Maintenance Schedule
- [ ] Monthly security updates
- [ ] Quarterly functionality review
- [ ] Annual major version planning

## Emergency Rollback Plan
In case anything goes wrong:

```bash
# Restore database
ddev import-db --src=backup-before-updates.sql.gz

# Restore codebase
git reset --hard [commit-hash-before-updates]
ddev composer install
```

## Success Metrics
- [ ] Site loads without errors
- [ ] All 8 pages display correctly
- [ ] Admin functionality intact
- [ ] No security vulnerabilities
- [ ] Performance maintained or improved

## Estimated Timeline
- **Phase 1-2:** 1 week
- **Phase 3-4:** 1-2 weeks  
- **Phase 5:** 2 weeks
- **Phase 6-7:** 1 week
- **Total:** 5-6 weeks for complete upgrade

## Resources Needed
- Full database backups before each phase
- Test environment (current DDEV setup is perfect)
- Time for thorough testing between phases
- Plan for temporary downtime during core upgrades

---

**Next Immediate Action:** Start with Phase 1 safe updates. These can be done right now with minimal risk.
