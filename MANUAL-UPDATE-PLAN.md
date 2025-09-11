# Manual Module Update Plan
**Started:** September 11, 2025
**Strategy:** Conservative manual updates, one module at a time
**Current Status:** Site stable and functional

## Pre-Update Checklist ✅

### 1. Current Module Inventory
Based on our previous assessment, here are the modules to update:

**Priority 1 - Security Critical:**
- `pathauto` (currently unknown version → target: 8.x-1.8)
- `honeypot` (currently unknown version → target: 8.x-1.30)
- `token` (currently unknown version → target: 8.x-1.10)

**Priority 2 - Stability Important:**
- `paragraphs` (currently unknown version → target: 8.x-1.12)
- `ctools` (currently unknown version → target: 8.x-3.4)
- `field_group` (currently unknown version → target: 8.x-1.3)

**Priority 3 - Enhancement:**
- `colorbox` (install new version: 8.x-1.6 - D9 compatible)
- `entity_reference_revisions` (currently unknown version → target: 8.x-1.9)
- `google_analytics` (currently unknown version → target: 8.x-3.1)

### 2. Backup Strategy
- ✅ Database backup before each module
- ✅ File system backup of current state
- ✅ Git commit before each change
- ✅ Module-specific backup before replacement

### 3. Testing Protocol (per module)
- [ ] Module replacement successful
- [ ] No PHP errors in logs
- [ ] Admin interface accessible
- [ ] Content pages load correctly
- [ ] Paragraphs functionality intact
- [ ] Forms still work
- [ ] No broken links
- [ ] Performance maintained

## Phase 1: Security Updates (Week 1)

### Day 1: Pathauto Update
**Current:** Unknown → **Target:** 8.x-1.8
**Why:** URL aliasing security and D9 preparation
**Risk:** Low (core functionality module)

### Day 2: Honeypot Update  
**Current:** Unknown → **Target:** 8.x-1.30
**Why:** Spam protection security fixes
**Risk:** Very Low (anti-spam only)

### Day 3: Token Update
**Current:** Unknown → **Target:** 8.x-1.10
**Why:** Token replacement security patches
**Risk:** Low (widely used, stable)

## Phase 2: Stability Updates (Week 2)

### Day 4: Paragraphs Update
**Current:** Unknown → **Target:** 8.x-1.12
**Why:** Core content building functionality
**Risk:** Medium (critical for content structure)
**Extra Testing:** All paragraph types, content display

### Day 5: CTools Update
**Current:** Unknown → **Target:** 8.x-3.4
**Why:** API improvements, D9 compatibility
**Risk:** Medium (affects other modules)

### Day 6: Field Group Update
**Current:** Unknown → **Target:** 8.x-1.3
**Why:** Form display improvements
**Risk:** Low (display only)

## Phase 3: Enhancement Updates (Week 3)

### Day 7: Colorbox Installation
**Action:** Install fresh 8.x-1.6
**Why:** Replace removed colorbox_load with proper version
**Risk:** Low (new clean installation)

### Day 8: Entity Reference Revisions
**Current:** Unknown → **Target:** 8.x-1.9
**Why:** Paragraphs dependency, D9 preparation
**Risk:** Medium (affects paragraphs)

### Day 9: Google Analytics Update
**Current:** Unknown → **Target:** 8.x-3.1
**Why:** Latest tracking features, privacy compliance
**Risk:** Low (tracking only)

## Update Process Template

### Before Each Module Update:
```bash
# 1. Database backup
ddev export-db --file=backup-before-[MODULE]-$(date +%Y%m%d-%H%M).sql.gz

# 2. Git commit current state
git add .
git commit -m "Before [MODULE] update - stable state"

# 3. Backup current module
cp -r modules/contrib/[MODULE] modules/contrib/[MODULE].backup

# 4. Check current version
ddev drush pm:list --type=module --status=enabled | grep [MODULE]
```

### Update Steps:
```bash
# 1. Download new version to temp directory
mkdir -p temp-updates
cd temp-updates
wget https://ftp.drupal.org/files/projects/[MODULE]-8.x-[VERSION].tar.gz
tar -xzf [MODULE]-8.x-[VERSION].tar.gz

# 2. Replace module files
cd ..
rm -rf modules/contrib/[MODULE]
mv temp-updates/[MODULE] modules/contrib/

# 3. Run database updates
ddev drush updb -y

# 4. Clear all caches
ddev drush cr
```

### Testing Steps:
```bash
# 1. Check for errors
ddev logs | tail -50

# 2. Test site functionality
ddev exec php test-site-functionality.php

# 3. Manual testing
ddev launch
# Test: homepage, content pages, admin, forms

# 4. Check module status
ddev drush pm:list --type=module --status=enabled | grep [MODULE]
```

### If Problems Occur:
```bash
# Rollback steps
rm -rf modules/contrib/[MODULE]
mv modules/contrib/[MODULE].backup modules/contrib/[MODULE]
ddev drush updb -y
ddev drush cr

# Restore database if needed
ddev import-db --src=backup-before-[MODULE]-[TIMESTAMP].sql.gz
```

## Success Criteria

### Per Module:
- ✅ Module installs without errors
- ✅ Site loads with HTTP 200
- ✅ No PHP errors in logs
- ✅ All content accessible
- ✅ Admin interface functional
- ✅ Module's specific functionality works

### Overall Project:
- ✅ All Priority 1 modules updated (security)
- ✅ Site performance maintained or improved
- ✅ No functionality lost
- ✅ Better D9 compatibility preparation
- ✅ Clean git history of changes

## Rollback Plan

If any update causes issues:
1. **Immediate:** Restore module files from backup
2. **Database:** Restore from pre-update backup
3. **Verify:** Run full functionality test
4. **Document:** Record what went wrong
5. **Alternative:** Research alternative module or approach

---

**Next Step:** Begin with Phase 1, Day 1 - Pathauto Update
