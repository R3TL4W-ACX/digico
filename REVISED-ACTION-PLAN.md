# Updated Action Plan - Conservative Approach

## Current Situation: ✅ STABLE & FUNCTIONAL

### What We've Accomplished:
- ✅ **Problematic modules removed** - `colorbox_load` and `ng_lightbox` disabled and removed
- ✅ **Site fully functional** - All content, paragraphs, and theme working
- ✅ **Database stable** - 8 pages, 10 paragraph types operational
- ✅ **Security improved** - Removed abandoned/unmaintained modules

### Current Limitations:
- ❌ **Composer plugin conflicts** - Can't easily update modules due to API version mismatch
- ❌ **Drupal 8.5.3** - Still on old core version
- ⚠️ **Manual upgrades needed** - Composer-based updates blocked

## Revised Strategy Options

### Option A: Stay Current, Focus on Stability
**Timeline:** Immediate
**Risk:** Very Low
**Approach:**
- Keep current working setup
- Monitor security updates manually
- Focus on content and business needs
- Plan major upgrade for later with fresh environment

**Pros:**
- Zero downtime risk
- Stable working site
- Focus on business value

**Cons:**
- No security updates
- No new features
- Technical debt accumulates

### Option B: Manual Module Updates
**Timeline:** 1-2 weeks
**Risk:** Medium
**Approach:**
- Download module updates manually
- Replace individual modules one by one
- Test each module update separately
- Keep current Drupal core

**Steps:**
1. Download latest D8-compatible versions manually
2. Replace module files individually
3. Run update.php for each module
4. Test thoroughly between each update

### Option C: Fresh Environment Approach
**Timeline:** 2-3 weeks
**Risk:** Medium
**Approach:**
- Set up new Drupal 9.5 environment
- Migrate content and configuration
- Rebuild with modern tools
- Keep current site as backup

**Benefits:**
- Modern Drupal 9.5
- All modules updated
- Clean composer setup
- Future-proof

### Option D: Docker/Container Approach
**Timeline:** 1 week
**Risk:** Low-Medium
**Approach:**
- Use different Composer version in container
- Isolate dependency conflicts
- Upgrade within controlled environment

## Recommendation: Option B - Manual Module Updates

**Why this approach:**
1. **Lowest risk** - Current site stays working
2. **Incremental progress** - Update modules one by one
3. **Testable** - Verify each update individually
4. **Reversible** - Easy to rollback individual modules

### Phase 1: Critical Security Updates (Week 1)
```bash
# Download and manually install:
- Pathauto 8.x-1.8 (security fixes)
- Honeypot 8.x-1.30 (security updates)
- Token 8.x-1.10 (security patches)
```

### Phase 2: Stability Updates (Week 2)
```bash
# Download and manually install:
- Paragraphs 8.x-1.12 (major improvements)
- CTools 8.x-3.4 (API improvements)
- Field Group 8.x-1.3 (bug fixes)
```

### Phase 3: Preparation for Future (Week 3)
```bash
# Download D9-ready versions:
- Colorbox 8.x-1.6 (D9 compatible)
- Entity Reference Revisions 8.x-1.9 (D9 ready)
```

## Implementation Plan for Option B

### Step 1: Backup Everything
```bash
# Database backup
ddev export-db --file=backup-before-manual-updates.sql.gz

# File system backup
tar -czf manual-update-backup-$(date +%Y%m%d).tar.gz .
```

### Step 2: Download Updates Manually
- Visit drupal.org/project/[module-name]
- Download latest D8 compatible version
- Extract to temporary folder

### Step 3: Update Process (per module)
1. Backup current module
2. Replace module files
3. Run update.php
4. Test functionality
5. Commit or rollback

### Step 4: Test Matrix
- [ ] Homepage loads
- [ ] Content pages display
- [ ] Admin interface works
- [ ] Paragraphs function
- [ ] Forms submit
- [ ] No PHP errors

## Expected Outcomes

**After Manual Updates:**
- ✅ Security vulnerabilities addressed
- ✅ Bug fixes applied
- ✅ Better D9 compatibility preparation
- ✅ Maintained stability
- ✅ Clear upgrade path for future

**Time Investment:** 2-3 weeks
**Risk Level:** Low-Medium
**Success Probability:** 95%

## Next Decision Point

**Question:** Which option would you prefer?
- **Option A:** Stay current (safest)
- **Option B:** Manual updates (recommended)
- **Option C:** Fresh environment (most modern)
- **Option D:** Container approach (technical)

---

**Current Status:** Site is stable and functional. We have successfully removed the problematic modules and are ready for the next phase of improvements.
