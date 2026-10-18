<?php $addressDetails=$data['addressDetails']??[];$fieldClass='mt-2 w-full rounded-2xl border border-gray-300 px-5 py-3.5'; ?>
<fieldset class="address-block rounded-2xl border border-gray-200 p-4 space-y-3">
<legend class="text-sm font-medium"><?= $isEnglish?'Address':'آدرس' ?></legend>
<div class="grid gap-3 sm:grid-cols-2">
<label class="text-sm"><?= $isEnglish?'Province':'استان' ?><select name="province_id" class="<?= $fieldClass ?>"><option value=""><?= $isEnglish?'Select province':'انتخاب استان' ?></option><?php foreach($data['provinces']??[] as $province): ?><option value="<?= (int)$province['province_id'] ?>" <?= (int)($addressDetails['province_id']??0)===(int)$province['province_id']?'selected':'' ?>><?= e($province['province_name']) ?></option><?php endforeach; ?></select></label>
<label class="text-sm"><?= $isEnglish?'City':'شهر' ?><select name="county_id" class="<?= $fieldClass ?>"><option value=""><?= $isEnglish?'Select city':'انتخاب شهر' ?></option><?php foreach($data['counties']??[] as $county): ?><option data-province="<?= (int)$county['province_id'] ?>" value="<?= (int)$county['county_id'] ?>" <?= (int)($addressDetails['county_id']??0)===(int)$county['county_id']?'selected':'' ?>><?= e($county['county_name']) ?></option><?php endforeach; ?></select></label>
</div>
<label class="block text-sm"><?= $isEnglish?'Street address *':'ادامه آدرس *' ?><input name="address" required value="<?= e((string)($profile['address']??'')) ?>" class="<?= $fieldClass ?>"></label>
<div class="grid gap-3 sm:grid-cols-3">
<?php foreach(['postal_code'=>['Postal code','کد پستی'],'latitude'=>['Latitude','عرض جغرافیایی'],'longitude'=>['Longitude','طول جغرافیایی']] as $key=>$labels): ?>
<label class="text-sm"><?= $labels[$isEnglish?0:1] ?><input name="<?= $key ?>" dir="ltr" inputmode="<?= $key==='postal_code'?'numeric':'decimal' ?>" value="<?= e((string)($addressDetails[$key]??'')) ?>" class="<?= $fieldClass ?>"></label>
<?php endforeach; ?>
</div>
<button type="button" data-enrollment-map class="text-sm text-indigo-600"><i class="fas fa-map-marker-alt"></i> <?= $isEnglish?'Choose on map':'انتخاب روی نقشه' ?></button>
</fieldset>
