<div class="form-group col-lg-6">
    <label for="nom">Nom</label>
    <input type="text" class="form-control" id="nom" name="nom" value="{{ old('nom', $beneficiaire->nom ?? '') }}" required>
</div>
<div class="form-group col-lg-6">
    <label for="prenom">Prénom</label>
    <input type="text" class="form-control" id="prenom" name="prenom" value="{{ old('prenom', $beneficiaire->prenom ?? '') }}" required>
</div>
<div class="form-group">
    <label for="adresse">Address</label>
    <input type="text" class="form-control" id="adresse" name="adresse" value="{{ old('adresse', $beneficiaire->adresse ?? '') }}" required>
</div>
<div class="form-group col-lg-3">
    <label for="cp">Postal Code</label>
    <input type="text" class="form-control" id="cp" name="cp" value="{{ old('cp', $beneficiaire->cp ?? '') }}" required>
</div>
<div class="form-group col-lg-9">
    <label for="ville">City</label>
    <input type="text" class="form-control" id="ville" name="ville" value="{{ old('ville', $beneficiaire->ville ?? '') }}" required>
</div>
<div class="form-group">
    <label for="telephone">Phone</label>
    <input type="text" class="form-control" id="telephone" name="telephone" value="{{ old('telephone', $beneficiaire->telephone ?? '') }}" required>
</div>
<div class="form-group">
    <label for="telephone_2">Phone 2</label>
    <input type="text" class="form-control" id="telephone_2" name="telephone_2" value="{{ old('telephone_2', $beneficiaire->telephone_2 ?? '') }}">
</div>
<div class="form-group">
    <label for="email">Email</label>
    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $beneficiaire->email ?? '') }}" required>
</div>
<div class="form-group">
    <label for="menage_mpr">Menage MPR</label>
    <select class="form-control" id="menage_mpr" name="menage_mpr" required>
        <option value="" {{ old('menage_mpr', $beneficiaire->menage_mpr ?? '') == '' ? 'selected' : '' }}></option>
        <option value="bleu" {{ old('menage_mpr', $beneficiaire->menage_mpr ?? '') == 'bleu' ? 'selected' : '' }}>Bleu</option>
        <option value="jaune" {{ old('menage_mpr', $beneficiaire->menage_mpr ?? '') == 'jaune' ? 'selected' : '' }}>Jaune</option>
        <option value="violet" {{ old('menage_mpr', $beneficiaire->menage_mpr ?? '') == 'violet' ? 'selected' : '' }}>Violet</option>
        <option value="rose" {{ old('menage_mpr', $beneficiaire->menage_mpr ?? '') == 'rose' ? 'selected' : '' }}>Rose</option>
    </select>
</div>
<div class="form-group">
    <label for="chauffage">Type de chauffage</label>
    <select class="form-control" id="chauffage" name="chauffage" required>
        <option value="" {{ old('chauffage', $beneficiaire->chauffage ?? '') == '' ? 'selected' : '' }}></option>
        <option value="gaz" {{ old('chauffage', $beneficiaire->chauffage ?? '') == 'gaz' ? 'selected' : '' }}>Gaz</option>
        <option value="fioul" {{ old('chauffage', $beneficiaire->chauffage ?? '') == 'fioul' ? 'selected' : '' }}>Fioul</option>
        <option value="bois" {{ old('chauffage', $beneficiaire->chauffage ?? '') == 'bois' ? 'selected' : '' }}>Bois</option>
        <option value="charbon" {{ old('chauffage', $beneficiaire->chauffage ?? '') == 'charbon' ? 'selected' : '' }}>Charbon</option>
        <option value="electricite" {{ old('chauffage', $beneficiaire->chauffage ?? '') == 'electricite' ? 'selected' : '' }}>Electricite</option>
    </select>
</div>
<div class="form-group">
    <label for="occupation">Occupation</label>
    <select class="form-control" id="occupation" name="occupation" required>
        <option value="" {{ old('occupation', $beneficiaire->occupation ?? '') == '' ? 'selected' : '' }}></option>
        <option value="locataire" {{ old('occupation', $beneficiaire->occupation ?? '') == 'locataire' ? 'selected' : '' }}>Locataire</option>
        <option value="proprietaire" {{ old('occupation', $beneficiaire->occupation ?? '') == 'proprietaire' ? 'selected' : '' }}>Proprietaire</option>
    </select>
</div>

@if ($isCreate)
<div class="form-group">
    <label for="fiche_id">Type de dossier</label>
    <select class="form-control" id="fiche_id" name="fiche_id">
        <option value=""></option>
        @foreach($fiches as $fiche)
        <option value="{{$fiche->id}}">{{$fiche->fiche_name}}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label for="mandataire_financier">Mon Accompagnateur Renov MAR</label>
    <select class="form-control" id="mandataire_financier" name="mandataire_financier">
        <option value=""></option>
        @foreach($financiers as $financier)
        <option value="{{$financier->id}}">{{$financier->client_title}}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label for="mandataire_administratif">Mandataire Financier</label>
    <select class="form-control" id="mandataire_administratif" name="mandataire_administratif">
        <option value=""></option>
        @foreach($administratifs as $administratif)
        <option value="{{$administratif->id}}">{{$administratif->client_title}}</option>
        @endforeach
    </select>
</div>

@if($user->client && $user->client->type_client==3) 

<input type="hidden" name="installateur" value="{{$user->client->id}}">
@else
<div class="form-group">
    <label for="installateur">Installateur</label>
    <select class="form-control" id="installateur" name="installateur">
        <option value=""></option>
        @foreach($installateurs as $installateur)
        <option value="{{$installateur->id}}">{{$installateur->client_title}}</option>
        @endforeach
    </select>
</div>
@endif

@endif
