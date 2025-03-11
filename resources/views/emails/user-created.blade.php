@extends('emails.base')

@section('title', 'Your Account has been Created')

@section('content')



<table data-structure-type="row" data-row-type="FULL" data-row-id="57ad1534-ab00-5ed4-abb7-62905813d115" data-row-behavior="NORMAL" data-row-repeat-count="5" data-row-sort-products="Orders" data-row-background-color-wide="transparent" cellpadding="0" cellspacing="0" width="100%" class="newsletter-row row57ad1534 non-responsive" bgcolor="transparent">
    <tbody>
        <tr>
            <td class="row-td" align="center">
                <table class="inner-row-table" cellpadding="0" cellspacing="0" width="600" valign="top" bgcolor="transparent" style="background-repeat: no-repeat; background-position: initial; border-radius: 0px;">
                    <tbody class="row-table-body" style="display: table; width: 600px;">
                        <tr>
                            <td class="slot slot32262670 FULL" data-structure-type="slot" data-slot-type="FULL" width="600" cellpadding="0" cellspacing="0" align="left" valign="top" style="font-weight: normal; max-width: 600px; width: 600px; border-radius: 0px; overflow: visible;">
                                <table class="component spacer-component spacerb67562e6" data-component-type="spacer" cellspacing="0" cellpadding="0" width="600" align="top" style="background-color: transparent; clear: both; height: 40px; border-width: 0px; border-radius: 0px; border-color: rgb(0, 0, 0); border-style: unset; border-collapse: initial;">
                                    <tbody>
                                        <tr>
                                            <td height="40" style="height: 40px;">
                                                <div style="display: none; font-size: 1px;"> </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="component image-component imagef9895cdd" data-component-type="image" data-parent-slot-type="FULL" cellspacing="0" cellpadding="0" width="600" style="clear: both; background-color: transparent;">
                                    <tbody>
                                        <tr class="newsletter-main-content">
                                            <td class="image image-container" align="center" style="line-height: 1px; padding: 0px;"><img src="https://crm.genius-market.fr/frontend/assets/img/logo%20genius.png" alt="Email Image" class="newsletter-image " height="auto" width="85" data-resize-width="85" data-resize-height="98" data-original-src="https://crm.genius-market.fr/frontend/assets/img/logo%20genius.png" align="bottom" style="box-sizing: border-box; display: inline-block; border-width: 0px; border-color: rgb(0, 0, 0); border-radius: 0px; border-style: unset;"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="component text-component text-desktop text592e5494" data-component-type="text" cellspacing="0" cellpadding="0" width="600" style="clear: both; background-color: transparent;">
                                    <tbody>
                                        <tr>
                                            <td class="element-td text-element-td" style="overflow-wrap: break-word; word-break: break-word;">
                                                <div class="text_container newsletter-main-content" style="padding: 10px; border-width: 30px; border-color: transparent; border-radius: 0px; border-style: solid; color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif, Arial, Helvetica, sans-serif; line-height: 1.3; font-size: 16px;">
                                                    <h1>Bonjour {{ $user->name }},</h1>
                                                    <p>Votre compte a été créé sur le CRM. Voici vos détails de connexion</p>
                                                    <p><strong>Email:</strong> {{ $user->email }}</p>
                                                    <p><strong>Mot de passe temporaire :</strong> {{ $temporaryPassword }}</p>
                                                    <p>Merci de changer votre mot de passe en vous connectant au lien suivant :</p>
                                                    {{-- <a class="btn btn-primary" href="{{ $url }}">Réinitialisation du mot de passe</a> --}}
                                                    <p>Merci!</p>
                                                </div>
                                                <div>
                                                    <!--[if gte mso 15]><div style="display: 'none'; font-size: 1px; line-height: 1px;"> </div><![endif]-->
                                                </div>
                                                <!--[if gte mso 15]><div style="display: none; font-size: 1px; line-height: 1px;"> </div><![endif]-->
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="component text-component text-mobile text592e5494" data-component-type="text" data-repeatable-tag="" cellspacing="0" cellpadding="0" width="600" style="clear: both; display: none; background-color: transparent;">
                                    <tbody>
                                        <tr>
                                            <td class="element-td text-element-td" style="overflow-wrap: break-word; word-break: break-word;">
                                                <div class="text_container newsletter-main-content" style="padding: 10px; border-width: 20px; border-color: transparent; border-radius: 0px; border-style: solid; color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif, Arial, Helvetica, sans-serif; line-height: 1.3; font-size: 16px;">
                                                    <h1>Bonjour {{ $user->name }},</h1>
                                                    <p>Votre compte a été créé sur le CRM. Voici vos détails de connexion</p>
                                                    <p><strong>Email:</strong> {{ $user->email }}</p>
                                                    <p><strong>Mot de passe temporaire :</strong> {{ $temporaryPassword }}</p>
                                                    <p>Merci de changer votre mot de passe en vous connectant au lien suivant :</p>
                                                    {{-- <a class="btn btn-primary" href="{{ $url }}">Réinitialisation du mot de passe</a> --}}
                                                    <p>Merci!</p>
                                                </div>
                                                <div>
                                                    <!--[if gte mso 15]><div style="display: 'none'; font-size: 1px; line-height: 1px;"> </div><![endif]-->
                                                </div>
                                                <!--[if gte mso 15]><div style="display: none; font-size: 1px; line-height: 1px;"> </div><![endif]-->
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="component spacer-component spacerdaedeb08" data-component-type="spacer" cellspacing="0" cellpadding="0" width="600" align="top" style="background-color: transparent; clear: both; height: 20px; border-width: 0px; border-radius: 0px; border-color: rgb(0, 0, 0); border-style: unset; border-collapse: initial;">
                                    <tbody>
                                        <tr>
                                            <td height="20" style="height: 20px;">
                                                <div style="display: none; font-size: 1px;"> </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table data-component-type="button" class="component button-component buttonef3842cb" cellspacing="0" cellpadding="0" width="600" border="0" style="width: 600px; vertical-align: top; background-color: transparent; clear: both;">
                                    <tbody>
                                        <tr class="newsletter-main-content">
                                            <td class="element-td" align="center" style="padding: 0px;">
                                                <table class="button-wrapper" cellspacing="0" align="center" border="0" cellpadding="12" style="line-height: normal; vertical-align: baseline; text-align: center; border-collapse: separate; min-width: 10px; background-color: rgb(0, 0, 0);">
                                                    <tbody>
                                                        <tr class="mobile-button--tr">
                                                            <td width="100%" align="center" style="padding: 0px; width: 250px; height: 60px;"><a class="newsletter-button-link" href="{{ $url }}" style="height: 100%; line-height: normal; display: inline-block; text-align: center; color: rgb(255, 255, 255); font-family: Montserrat, Arial, Helvetica, sans-serif; font-style: normal; font-size: 18px; font-weight: normal; text-decoration: none !important;" target="_blank"><span class="button" style="color: rgb(255, 255, 255); display: block; min-width: 10px; padding-top: 21px; padding-bottom: 21px;">Réinitialisation</span></a></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="component spacer-component spacer9f087d4b" data-component-type="spacer" cellspacing="0" cellpadding="0" width="600" align="top" style="background-color: transparent; clear: both; height: 60px; border-width: 0px; border-radius: 0px; border-color: rgb(0, 0, 0); border-style: unset; border-collapse: initial;">
                                    <tbody>
                                        <tr>
                                            <td height="60" style="height: 60px;">
                                                <div style="display: none; font-size: 1px;"> </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                {{-- <table class="component social-follow-component socialfollow5313ab88" data-component-type="social_follow" cellspacing="0" cellpadding="0" width="600" align="center" data-icon-style="black-round" style="clear: both; table-layout: fixed; background-color: transparent;">
                                    <tbody>
                                        <tr class="newsletter-main-content">
                                            <td align="center" style="padding: 0px;">
                                                <table cellspacing="0" cellpadding="0" style="text-align: center;">
                                                    <tbody>
                                                        <tr class="social-follow-row" style="text-align: center;">
                                                            <td><a></a></td>
                                                            <td class="social-icon-spacing" width="5" height="10" style="display: inline-block;"></td>
                                                            <td class="social-icon-td" style="display: inline-block;"><a data-icon-type="twitter" href="" class="social_icon twitter" style="display: block; cursor: pointer; text-decoration: none !important;" target="_blank"><img srcset="https://cdn-editor.moosend.com/assets/images/social_icons/social_follow/black-round/twitter@3x.png 3x, https://cdn-editor.moosend.com/assets/images/social_icons/social_follow/black-round/twitter@2x.png 2x, https://cdn-editor.moosend.com/assets/images/social_icons/social_follow/black-round/twitter.png 1x" src="https://cdn-editor.moosend.com/assets/images/social_icons/social_follow/black-round/twitter.png" alt="social icon" border="0" style="vertical-align: bottom; display: block;"></a></td>
                                                            <td class="social-icon-spacing" width="5" height="10" style="display: inline-block;"></td>
                                                            <td class="social-icon-spacing" width="5" height="10" style="display: inline-block;"></td>
                                                            <td class="social-icon-td" style="display: inline-block;"><a data-icon-type="linkedin" href="" class="social_icon linkedin" style="display: block; cursor: pointer; text-decoration: none !important;" target="_blank"><img srcset="https://cdn-editor.moosend.com/assets/images/social_icons/social_follow/black-round/linkedin@3x.png 3x, https://cdn-editor.moosend.com/assets/images/social_icons/social_follow/black-round/linkedin@2x.png 2x, https://cdn-editor.moosend.com/assets/images/social_icons/social_follow/black-round/linkedin.png 1x" src="https://cdn-editor.moosend.com/assets/images/social_icons/social_follow/black-round/linkedin.png" alt="social icon" border="0" style="vertical-align: bottom; display: block;"></a></td>
                                                            <td class="social-icon-spacing" width="5" height="10" style="display: inline-block;"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="component spacer-component spacer767aaddf" data-component-type="spacer" cellspacing="0" cellpadding="0" width="600" align="top" style="background-color: transparent; clear: both; height: 20px; border-width: 0px; border-radius: 0px; border-color: rgb(0, 0, 0); border-style: unset; border-collapse: initial;">
                                    <tbody>
                                        <tr>
                                            <td height="20" style="height: 20px;">
                                                <div style="display: none; font-size: 1px;"> </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="component text-component text-desktop text91b53156" data-component-type="text" cellspacing="0" cellpadding="0" width="600" style="clear: both; background-color: transparent;">
                                    <tbody>
                                        <tr>
                                            <td class="element-td text-element-td" style="overflow-wrap: break-word; word-break: break-word;">
                                                <div class="text_container newsletter-main-content" style="padding: 10px; border-width: 0px; border-color: rgb(0, 0, 0); border-radius: 0px; border-style: unset; color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif, Arial, Helvetica, sans-serif; line-height: 1.3; font-size: 16px;">
                                                    <div style="text-align: center; line-height: 1.3;"><span style="font-size: 11px;"><a title="https://moosend.com/" href="https://eur02.safelinks.protection.outlook.com/?url=https%3A%2F%2Fwww.elfster.com%2F&data=05%7C01%7Cfotini%40moosend.com%7Ca7664f1a59734a288bf408dbe9a61f1a%7C91700184c3144dc9bb7ea411df456a1e%7C0%7C0%7C638360671941528067%7CUnknown%7CTWFpbGZsb3d8eyJWIjoiMC4wLjAwMDAiLCJQIjoiV2luMzIiLCJBTiI6Ik1haWwiLCJXVCI6Mn0%3D%7C3000%7C%7C%7C&sdata=jDdnwI99IY7tU4z7baFX%2BiIazxnbYEFFL1qs5usDG5k%3D&reserved=0" target="_blank" rel="noopener" data-linkindex="9" data-auth="Verified" style="text-decoration: none !important;"><span style="color: rgb(224, 62, 45);">crm.genius-market.fr</span></a><span style="color: rgb(99, 99, 99);"></span></span></div>
                                                    <div style="text-align: center; line-height: 1.3;"><span style="font-size: 11px;"><span style="color: rgb(224, 62, 45);">© 2023 Company</span><span style="color: rgb(99, 99, 99);"> | </span><span style="color: rgb(224, 62, 45);"><a style="color: rgb(224, 62, 45); text-decoration: none !important;" title="https://docs.moosend.com/users/moosend/en/customize-the-unsubscribe-settings-in-moosend.html" href="https://eur02.safelinks.protection.outlook.com/?url=https%3A%2F%2Fwww.elfster.com%2Femail%2Fnotifications%2F%3Fe%3D8ae28242-c20c-4e4e-98fc-ca247f66fafa&data=05%7C01%7Cfotini%40moosend.com%7Ca7664f1a59734a288bf408dbe9a61f1a%7C91700184c3144dc9bb7ea411df456a1e%7C0%7C0%7C638360671941528067%7CUnknown%7CTWFpbGZsb3d8eyJWIjoiMC4wLjAwMDAiLCJQIjoiV2luMzIiLCJBTiI6Ik1haWwiLCJXVCI6Mn0%3D%7C3000%7C%7C%7C&sdata=r98UHg1G9TowiYWKbQeJ9gRqu2Xoe3CmzRHYgTZjq8s%3D&reserved=0" target="_blank" rel="noopener" data-linkindex="10" data-auth="Verified">Change Email Settings</a></span></span></div>
                                                    <div style="text-align: center; line-height: 1.3;"><span style="font-size: 11px;"><span style="color: rgb(99, 99, 99);">You are receiving this email because you are registered at</span><span style="color: rgb(109, 109, 109);">Your Company</span></span></div>
                                                    <div style="text-align: center; line-height: 1.3;"><a href="#unsubscribeLink#" style="text-decoration: none !important;" target="_blank"><span style="font-size: 11px;"><span style="color: rgb(224, 62, 45);">Unsubscribe</span></span></a></div>
                                                    <p class="fix-android-mail" style="max-width: 580px; width: 100%; margin: 0px;"></p>
                                                </div>
                                                <div>
                                                    <!--[if gte mso 15]><div style="display: 'none'; font-size: 1px; line-height: 1px;"> </div><![endif]-->
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="component text-component text-mobile text91b53156" data-component-type="text" data-repeatable-tag="<div style='text-align: center; line-height: 1.3;'><span style='font-size: 11px;'><a title='https://moosend.com/' href='https://eur02.safelinks.protection.outlook.com/?url=https%3A%2F%2Fwww.elfster.com%2F&amp;data=05%7C01%7Cfotini%40moosend.com%7Ca7664f1a59734a288bf408dbe9a61f1a%7C91700184c3144dc9bb7ea411df456a1e%7C0%7C0%7C638360671941528067%7CUnknown%7CTWFpbGZsb3d8eyJWIjoiMC4wLjAwMDAiLCJQIjoiV2luMzIiLCJBTiI6Ik1haWwiLCJXVCI6Mn0%3D%7C3000%7C%7C%7C&amp;sdata=jDdnwI99IY7tU4z7baFX%2BiIazxnbYEFFL1qs5usDG5k%3D&amp;reserved=0' target='_blank' rel='noopener' data-linkindex='9' data-auth='Verified'><span style='color: rgb(224, 62, 45);'>yourcompany.com</span></a><span style='color: rgb(99, 99, 99);'>| Your company's address</span></span></div><div style='text-align: center; line-height: 1.3;'><span style='font-size: 11px;'><span style='color: rgb(224, 62, 45);'>© 2023 Company</span><span style='color: rgb(99, 99, 99);'> | </span><span style='color: rgb(224, 62, 45);'><a style='color: rgb(224, 62, 45);' title='https://docs.moosend.com/users/moosend/en/customize-the-unsubscribe-settings-in-moosend.html' href='https://eur02.safelinks.protection.outlook.com/?url=https%3A%2F%2Fwww.elfster.com%2Femail%2Fnotifications%2F%3Fe%3D8ae28242-c20c-4e4e-98fc-ca247f66fafa&amp;data=05%7C01%7Cfotini%40moosend.com%7Ca7664f1a59734a288bf408dbe9a61f1a%7C91700184c3144dc9bb7ea411df456a1e%7C0%7C0%7C638360671941528067%7CUnknown%7CTWFpbGZsb3d8eyJWIjoiMC4wLjAwMDAiLCJQIjoiV2luMzIiLCJBTiI6Ik1haWwiLCJXVCI6Mn0%3D%7C3000%7C%7C%7C&amp;sdata=r98UHg1G9TowiYWKbQeJ9gRqu2Xoe3CmzRHYgTZjq8s%3D&amp;reserved=0' target='_blank' rel='noopener' data-linkindex='10' data-auth='Verified'>Change Email Settings</a></span></span></div><div style='text-align: center; line-height: 1.3;'><span style='font-size: 11px;'><span style='color: rgb(99, 99, 99);'>You are receiving this email because you are registered at</span><span style='color: rgb(109, 109, 109);'>Your Company</span></span></div><div style='text-align: center; line-height: 1.3;'><a href=' #unsubscribeLink#'><span style='font-size: 11px;'><span style='color: rgb(224, 62, 45);'>Unsubscribe</span></span></a></div>" cellspacing="0" cellpadding="0" width="600" style="clear: both; display: none; background-color: transparent;">
                                    <tbody>
                                        <tr>
                                            <td class="element-td text-element-td" style="overflow-wrap: break-word; word-break: break-word;">
                                                <div class="text_container newsletter-main-content" style="padding: 0px 5px; border-width: 0px; border-color: rgb(0, 0, 0); border-radius: 0px; border-style: solid; color: rgb(0, 0, 0); font-family: Arial, Helvetica, sans-serif, Arial, Helvetica, sans-serif; line-height: 1.3; font-size: 16px;">
                                                    <div style="text-align: center; line-height: 1.3;"><span style="font-size: 11px;"><a title="https://moosend.com/" href="https://eur02.safelinks.protection.outlook.com/?url=https%3A%2F%2Fwww.elfster.com%2F&data=05%7C01%7Cfotini%40moosend.com%7Ca7664f1a59734a288bf408dbe9a61f1a%7C91700184c3144dc9bb7ea411df456a1e%7C0%7C0%7C638360671941528067%7CUnknown%7CTWFpbGZsb3d8eyJWIjoiMC4wLjAwMDAiLCJQIjoiV2luMzIiLCJBTiI6Ik1haWwiLCJXVCI6Mn0%3D%7C3000%7C%7C%7C&sdata=jDdnwI99IY7tU4z7baFX%2BiIazxnbYEFFL1qs5usDG5k%3D&reserved=0" target="_blank" rel="noopener" data-linkindex="9" data-auth="Verified" style="text-decoration: none !important;"><span style="color: rgb(224, 62, 45);">yourcompany.com</span></a><span style="color: rgb(99, 99, 99);">| Your company's address</span></span></div>
                                                    <div style="text-align: center; line-height: 1.3;"><span style="font-size: 11px;"><span style="color: rgb(224, 62, 45);">© 2023 Company</span><span style="color: rgb(99, 99, 99);"> | </span><span style="color: rgb(224, 62, 45);"><a style="color: rgb(224, 62, 45); text-decoration: none !important;" title="https://docs.moosend.com/users/moosend/en/customize-the-unsubscribe-settings-in-moosend.html" href="https://eur02.safelinks.protection.outlook.com/?url=https%3A%2F%2Fwww.elfster.com%2Femail%2Fnotifications%2F%3Fe%3D8ae28242-c20c-4e4e-98fc-ca247f66fafa&data=05%7C01%7Cfotini%40moosend.com%7Ca7664f1a59734a288bf408dbe9a61f1a%7C91700184c3144dc9bb7ea411df456a1e%7C0%7C0%7C638360671941528067%7CUnknown%7CTWFpbGZsb3d8eyJWIjoiMC4wLjAwMDAiLCJQIjoiV2luMzIiLCJBTiI6Ik1haWwiLCJXVCI6Mn0%3D%7C3000%7C%7C%7C&sdata=r98UHg1G9TowiYWKbQeJ9gRqu2Xoe3CmzRHYgTZjq8s%3D&reserved=0" target="_blank" rel="noopener" data-linkindex="10" data-auth="Verified">Change Email Settings</a></span></span></div>
                                                    <div style="text-align: center; line-height: 1.3;"><span style="font-size: 11px;"><span style="color: rgb(99, 99, 99);">You are receiving this email because you are registered at</span><span style="color: rgb(109, 109, 109);">Your Company</span></span></div>
                                                    <div style="text-align: center; line-height: 1.3;"><a href="#unsubscribeLink#" style="text-decoration: none !important;" target="_blank"><span style="font-size: 11px;"><span style="color: rgb(224, 62, 45);">Unsubscribe</span></span></a></div>
                                                    <p class="fix-android-mail" style="max-width: 596px; width: 100%; margin: 0px;"></p>
                                                </div>
                                                <div>
                                                    <!--[if gte mso 15]><div style="display: 'none'; font-size: 1px; line-height: 1px;"> </div><![endif]-->
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table> --}}
                                <table class="component spacer-component spacer3fbb0504" data-component-type="spacer" cellspacing="0" cellpadding="0" width="600" align="top" style="background-color: transparent; clear: both; height: 20px; border-width: 0px; border-radius: 0px; border-color: rgb(0, 0, 0); border-style: unset; border-collapse: initial;">
                                    <tbody>
                                        <tr>
                                            <td height="20" style="height: 20px;">
                                                <div style="display: none; font-size: 1px;"> </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>

@endsection
