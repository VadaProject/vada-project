<?php include 'config/db_connect.php'; ?>
<?php include 'includes/page_top.php'; ?>
<main class="page-container">
    <h2>User Guide</h2>
    <h3>Support Means</h3>
    <hr>
    <p>
        Go to <a href="#Inference">Inference</a>.
    <p>
        Go to <a href="#Testimony">Testimony</a>.
    <p>
        Go to <a href="#Perception">Perception</a>.
        <hr>
    <p>
        A claim is the most basic element of debate on the Vāda Project platform. Each claim consists of a <b>subject</b> (<i>pakṣa</i>) and a <b>target property</b> (<i>sādhya</i>). A subject is what a claim is about. A target property is what a claim asserts about a subject. For example, in the claim “The hill is on fire,” the subject is “the hill” and the target property is “is on fire.”
    <p>
        When entering a claim, a user will be prompted to enter a subject and a target property. To minimize grammatical bugs in later statements, the user will be prompted to indicate whether the subject is a person or a thing.
    <p>
        All claims entered on the Vāda Project platform must be justified through one of four <b>support means</b>. Support means are distinct ways of supporting claims. In this iteration of the Vāda Project platform, support means include three <b>knowledge sources</b> (<i>pramāṇa</i>s) and <b>philosophical argument</b> (<i>tarka</i>).
    <p>
        On the Vāda Project platform, philosophical argument is a method of administratively moderated argument that is conducted through a Facebook plugin; as Vātsyāyana writes in his commentary on <i>Nyāya Sūtra</i>1.1.40, in a <i>vāda</i> debate, <i>tarka</i> serves only a supplementary role to debate that is conducted through appeal to knowledge sources.<a class="sdendnoteanc" name="sdendnote1anc" href="#sdendnote1sym"><sup>i</sup></a>
        The three knowledge sources in this iteration of the Vāda Project platform are <b>inference</b>, <b>testimony</b>, and <b>perception</b>.
    <h3 id="Inference">Inference</h3>
    <p>
        Inference (<i>anumāna</i>) is a way of generating knowledge about something that is presently uncertain through knowledge about something that is presently certain. In a <i>vāda</i> debate, all initial claims should be controversial, or uncertain. To support a claim through inference, a user asserts that the claim’s subject is known to possess a <b>reason</b> <b>property</b>(<i>hetu</i>) that is invariably present with the claim’s target property.

    <p>
        For example, a user might select “inference” as a support means to justify the claim, “The hill is on fire.” Afterwards, when prompted to enter a reason property, the user might type “has smoke billowing from it” in the “reason property” field.
    <p>
        To help to establish that the reason property is known to be invariably present with the target property, the user will be prompted to enter an <b>example</b> of a similar subject (<i>sapakṣa</i>). An appropriate similar subject will differ from the subject of the claim (the hill) and be known to possess both the reason property and the target property of the inference. For example, the user might enter “a kitchen” in the “example” field and click “submit.”
    <p>
        After clicking “submit,” the user will be notified that the claim has been submitted. Upon returning to the debate topic page, users will see that the claim has been added. By clicking on the “details” link on the claim box, users will see that the inference has automatically generated the following three statements:
    <p>
        Thesis: The hill is on fire.
    <p>
        Reason: The hill has smoke billowing from it.
    <p>
        Rule &amp; Example: Whatever has smoke billowing from it is on fire, as in the case of a kitchen.
    <p>
        The thesis statement of the inference conjoins the subject with the target property of the claim. The reason statement conjoins the same subject with the reason property. The rule and example statement is an automatically generated assertion that the target property is present in whatever the reason property is present in, and that a similar subject exemplifies this universal association.
    <p>
        If they would like, users can now raise doubts about the claim by clicking the “<b>Flag this claim</b>” tab from the “details” pop-up. Flags that can be raised against a claim are specific to the knowledge source that supports it.
    <p>
        The following flags can be raised against the thesis, reason, and rule &amp; example statements of a claim supported by inference:
        <style type="text/css">
            table,
            td,
            th {
                border: 1px solid;
            }

            th {
                color: black;
            }
        </style>
    <div class="ritz grid-container">
        <h4>Hetvābhasas (Reason Flags)</h4>
        <table>
            <tr>
                <th>Flag Name</td>
                <th>Explanation</td>
                <th>Example</td>
                <th>Counter-claim</td>
            </tr>
            <tr>
                <td>Unestablished Subject (aśrayāsiddha)</td>
                <td>The subject is either ambiguous, in doubt, or non-existent.</td>
                <td>&quot;Sentient extraterrestial aliens are vegetarian.&quot;</td>
                <td>&quot;Sentient extraterrestial aliens are not known to exist.&quot;</td>
            </tr>
            <tr>
                <td>Itself Unestablished (svarupāsiddha)</td>
                <td>The reason property is not present, or is not known to be present, in the subject.</td>
                <td>&quot;These nutritional supplements are healthy, because these nutritional supplements boost the immune system.&quot;</td>
                <td>&quot;These nutritional supplements are not known to boost the immune system.&quot;</td>
            </tr>
            <tr>
                <td>Too Narrow (asādhāraṇa)</td>
                <td>Either a) the example is ambiguous, in doubt, or non-existent; or b) the reason property is not present, or is not known to be present, with the target property in the example; or c) the example is the same as, or is contained within, the subject.</td>
                <td>&quot;Gods value critical examination, because gods value truth, as in the case of Loki.&quot;</td>
                <td>a) &quot;Loki isn&#39;t known to exist,&quot; b) &quot;Loki doesn&#39;t value truth and critical thinking,&quot; or c) &quot;Loki is included in the subject, &#39;gods&#39;.&quot;</td>
            </tr>
            <tr>
                <td>Too Broad -- Counterexample (sādhāraṇa)</td>
                <td>The reason property is present without the target property in a specifiable counterexample.</td>
                <td>&quot;I will pass the exam today, because I always pass exams when I wear this ring.&quot;</td>
                <td>&quot;You wore that ring during last week&#39;s exam and did not pass.&quot;</td>
            </tr>
            <tr>
                <td>Too Broad -- Unestablished Universal (vyāpyatvāsiddha)</td>
                <td>There is no causal or conceptual reason to assume a universal association between the reason property and the target property; therefore, unspecifiable counterexamples may exist.</td>
                <td>&quot;I will pass the exam today, because I always pass exams when I wear this ring.&quot;</td>
                <td>&quot;Student accessories do not factor into the exam&#39;s grading rubric.&quot;</td>
            </tr>
            <tr>

                <td>Contrived Universal (upādhi)</td>
                <td>The universal association between the reason property and the target property exists only because of the presence of an additional property (upādhi).</td>
                <td>&quot;I will pass the exam today, because I always pass exams when I wear this ring.&quot;</td>
                <td>&quot;As well as wearing the ring during every exam you have passed, you have also studied for every exam you have attempted while wearing the ring.</td>
            </tr>
        </table>
        <h4>Nigrahasthānas (Defeaters)</h4>
        <table>
            <tr>
                <th>Flag Name</th>
                <th>Explanation</th>
                <th>Example</th>
                <th>Counter-claim</th>
            </tr>
            <tr>
                <td>Has Rival (prakaraṇasama)</td>
                <td>An unflagged rival claim speaks or advocates for the antithesis.</td>
                <td>&quot;Vanilla ice cream is flavorful.&quot;</td>
                <td>&quot;Vanilla ice cream is not flavorful.&quot;</td>
            </tr>
            <tr>
                <td>Too Early</td>
                <td>This claim is not controversial; no party in the debate speaks or advocates for the antithesis.</td>
                <td>&quot;The grass is green.&quot;</td>
                <td>&quot;No one says that the grass isn&#39;t green.&quot;</td>
            </tr>
            <tr>
                <td>Too Late (kālātyāpadiṣṭa)</td>
                <td>This claim has already been flagged or discredited.</td>
                <td>&quot;The Earth is flat.&quot;</td>
                <td>&quot;This claim repeats claim #100, which has active flags against it.&quot;</td>
            </tr>
            <tr>
                <td>Hostile (viruddha)</td>
                <td>The reason property establishes that the target property is not present in the subject.</td>
                <td>&quot;Firearms should be accessible to children, because firearms are lethal.&quot;</td>
                <td>&quot;Firearms should not be accessible to children, because firearms are lethal, as in the case of poison.&quot;</td>
            </tr>
            </tbody>
        </table>
    </div>
    <table width=623 cellpadding=7 cellspacing=0>
        <col width=105>
        <col width=160>
        <col width=315>
        <tr valign=TOP>
            <td width=105 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Statement

            </td>
            <td width=160 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Flag

            </td>
            <td width=315 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Explanation

            </td>
        </tr>
        <tr valign=TOP>
            <td width=105 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Thesis:

            </td>
            <td width=160 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Has Rival

            </td>
            <td width=315 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    An unflagged
                    rival claim speaks or advocates for the antithesis.

            </td>
        </tr>
        <tr valign=TOP>
            <td width=105 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    <br>

            </td>
            <td width=160 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Too Early

            </td>
            <td width=315 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    This claim is
                    not controversial; no party in the debate speaks or advocates for
                    the antithesis.

            </td>
        </tr>
        <tr valign=TOP>
            <td width=105 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    <br>

            </td>
            <td width=160 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Too Late

            </td>
            <td width=315 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    This claim
                    has already been flagged or discredited in vāda debate

            </td>
        </tr>
        <tr valign=TOP>
            <td width=105 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Reason:

            </td>
            <td width=160 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Unestablished
                    Subject

            </td>
            <td width=315 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    The subject
                    is either ambiguous, not known to exist, or non-existent.

            </td>
        </tr>
        <tr valign=TOP>
            <td width=105 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    <br>

            </td>
            <td width=160 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Itself
                    Unestablished

            </td>
            <td width=315 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    The reason
                    property is not present, or is not known to be present, in the
                    subject.

            </td>
        </tr>
        <tr valign=TOP>
            <td width=105 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    <br>

            </td>
            <td width=160 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Hostile

            </td>
            <td width=315 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    The reason
                    property establishes, by inference, that the target property is
                    <i>not</i> present in the subject.

            </td>
        </tr>
        <tr valign=TOP>
            <td width=105 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Rule &amp;
                    Example:

            </td>
            <td width=160 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Too Narrow

            </td>
            <td width=315 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Either a) the
                    similar subject is ambiguous, not known to exist, or non-existent;
                    or b) the reason property is not present, or is not known to be
                    present, with the target property in the similar subject; or c)
                    the similar subject is the same as, or is contained within, the
                    subject.

            </td>
        </tr>
        <tr valign=TOP>
            <td width=105 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    <br>

            </td>
            <td width=160 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Too
                    Broad

                <p>
                    (Counterexample)

            </td>
            <td width=315 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    The reason
                    property is known to be present without the target property in a
                    <b>dissimilar subject</b> (<i>vipakṣa</i>);
                    i.e., a counterexample has the reason property but not the target
                    property.

            </td>
        </tr>
        <tr valign=TOP>
            <td width=105 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    <br>

            </td>
            <td width=160 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Too
                    Broad

                <p>
                    (Unestablished
                    Universal)

            </td>
            <td width=315 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    There is no
                    causal or conceptual reason to assume a universal association
                    between the reason property and the target property; therefore,
                    yet undiscovered counterexamples may exist.

            </td>
        </tr>
        <tr valign=TOP>
            <td width=105 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    <br>

            </td>
            <td width=160 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Contrived
                    Universal

            </td>
            <td width=315 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    The universal
                    association between the reason property and the target property
                    exists only because of the presence of an additional property
                    (<i>upādhi</i>).
            </td>
        </tr>
    </table>
    <h3 id="Testimony">Testimony</h3></span>
    <p>
        Testimony (<i>śabda</i>) is a way of generating knowledge about something previously uncertain through the words of a <b>trustworthy authority</b> (<i>āpta</i>). As Vātsyāyana writes in his commentary on <i>Nyāya Sūtra</i> 1.1.7, “A trustworthy authority is someone who knows something directly, an instructor with the desire to communicate it faithfully as it is known” (Dasti and Phillips: 35).
    <p>
        The Sanskrit term <i>śabda</i> means “word.” When one comes to know something through testimony, words are the cause or instrument of this knowledge.
    <p>
        Yogācāra Buddhist and other philosophers contested <i>śabda</i>’s status as a source of knowledge, and preferred to limit <i>pramāṇa</i>s in <i>vāda</i> debates to sources such as inference and perception. However, Nyāya philosophers argued that to refuse to acknowledge testimony as a <i>pramāṇa</i> would contradict lived epistemic practice. In their everyday lives, even Buddhists who claim to reject <i>śabda</i> recognize the Buddha’s words and other textual and verbal sources as trustworthy authorities, they argued.
    <p>
        Buddhist philosophers such as Dignāga and Dharmakīrti responded by attempting to show that in day-to-day life, knowing through words does not practically differ from knowing through inference.
    <p>
        For the sake of coordinating open, truth-directed debate about contemporary controversies among users of diverse backgrounds and perspectives, this iteration of the Vāda Project platform includes testimony as a source of knowledge. However, it might theoretically be possible to translate all verbal and textual claims made by supposedly trustworthy authorities into the standard logical form of an inference.
    <p>
        Users are expected to select inference as a support means for claims that various speakers and advocates attempt to justify, not by relying on the direct knowledge of particular, supposedly trustworthy authorities, but on the basis of recurring reasons and examples. For example, critics of abortion commonly put forward the following inference:
    <p>
        Thesis: Abortion should be illegal.
    <p>
        Reason: Abortion is murder.
    <p>
        Rule &amp; Example: Whatever is murder should be illegal, as in the case of infanticide.
    <p>
        When speakers and advocates use reasons, general rules, and examples to support claims without appealing to the direct knowledge of particular textual or verbal sources, inference is the appropriate support means.
    <p>
        Even if an argument appeals to the supposedly direct knowledge of a particular textual or verbal source, users can sometimes translate this argument into the standard logical form of an inference without much distortion. For example, specific scientific testimony in the area of fetal neurology<a class="sdendnoteanc" name="sdendnote2anc" href="#sdendnote2sym"><sup>ii</sup></a>
        might be translated into the following inference:
    <p>
        Thesis: A human fetus in the first trimester of pregnancy lacks capacity for sentience.
    <p>
        Reason: A human fetus in the first trimester of pregnancy lacks established and functional somatosensory pathways from the periphery to the primary somatosensory region of the cerebral cortex.
    <p>
        Rule and Example: Whatever lacks established and functional somatosensory pathways from the periphery to the primary somatosensory region of the cerebral cortex lacks capacity for sentience, as in the case of a human zygote.
    <p>
        However, to exclude <i>śabda</i> as a <i>pramāṇa</i> and insist that users be equipped to do such translation work would be both cumbersome and counterproductive to the purposes of this iteration of the Vāda Project platform.
    <p>
        First, extracting reason properties and examples from written and verbal sources that are composed in various rhetorical styles is typically an art and not a mechanical process. Because not all textual or verbal sources state specific reason properties or similar cases, translators usually need to create their own examples and reason properties either wholesale or from scattered textual elements. This sort of translation work can be time consuming and difficult; it is also unnecessary if testimony is made available, contingently, as a plausible support means for the Vāda Project platform.
    <p>
        Second, to insist that all textual and verbal testimony be translated into the standard logical form of an inference would become increasingly counterproductive as textual and verbal sources become more and more specialized. To be able to support technical claims whether through testimony or inference, users must have or be equipped to develop a degree of understanding in subject areas as varied, to use the topic of the inferences above as a case in point, as fetal neurology, privacy law, and criminal homicide codes. But to require that all users master these various technical subject areas to an extent that would enable them to translate specialized arguments into the standard logical form of an inference would limit the abilities of diverse users to debate controversial topics on the platform.
    <p>
        This first iteration of the Vāda Project platform therefore contingently includes <i>śabda</i> as a support means—to preclude testimony would impose unnecessary burdens and be counterproductive to the Vāda Project’s aim of coordinating open, truth-directed debate about contemporary controversies among users of diverse backgrounds and perspectives.
    <p>
        As stated above, testimony is a way of acquiring knowledge through the words of a <b>trustworthy authority</b> (<i>āpta</i>). Because well-intentioned actions can err, and because persons who faithfully desire to communicate what they know can misspeak or miswrite what they intend to convey, this iteration of the Vāda Project platform adopts the following definition of an <i>āpta</i>:
    <p>
        A trustworthy authority is a textual or verbal source that knows something directly, desires to communicate it faithfully as it is known, and fulfills this aim.
    <p>
        For Nyāya philosophers, a textual or verbal source’s status as an <i>āpta</i> depends on reality rather than on the subjective assessments of readers or hearers. It either is or is not the case that a source has direct knowledge about what is testified, faithfully intends to communicate what is known, and manages to do so, no matter what readers and hearers personally think. If, as a matter of fact, any condition is not met, then regardless of the authority’s credentials and social status, the authority is not trustworthy. Conversely, if all conditions are met, then the authority <i>is</i> trustworthy, regardless of the authority’s possible lack of credentials and social status or generally dodgy demeanor. As Vātsyāyana writes in his commentary on <i>Nyāya Sūtra</i>1.1.7, “To be trustworthy is to have direct knowledge of something. One who operates on this basis is a <b>trustworthy authority</b>. This criterion applies equally to sages, respected members of one’s own community, and those outside the fold” (Dasti and Phillips: 35).
    <p>
        To support a claim through testimony, users should select “testimony” as a support means for the claim. They will then be prompted to enter a URL that links to the textual or verbal source in a text box labeled “Enter Speech/Research Document,” and to transcribe, or copy and paste, relevant words from the source in a text box labeled “Relevant Excerpt. Include timestamps for audio/video, if applicable.” The transcription should be verbatim.
    <p>
        After clicking “submit,” the user will be notified that the claim has been submitted. Upon returning to the debate topic page, users will see that the claim has been added. By clicking on the “details” link on the claim box, users will see that the platform has generated a hyperlink to the textual or verbal source and displays the transcribed words.
    <p>
        If they would like, users can now raise doubts about the claim by clicking the “<b>Flag this claim</b>” tab from the “Details” pop-up. The following flags can be raised against a claim supported by testimony:
    <table width=623 cellpadding=7 cellspacing=0>
        <col width=201>
        <col width=393>
        <tr valign=TOP>
            <td width=201 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Flag
            </td>
            <td width=393 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Explanation
            </td>
        </tr>
        <tr valign=TOP>
            <td width=201 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    No Direct Familiarity
            </td>
            <td width=393 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    The source either lacks direct familiarity, or is not known to have direct familiarity, with the state of affairs that the claim is about.
            </td>
        </tr>
        <tr valign=TOP>
            <td width=201 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Errant or Uncertain Information
            </td>
            <td width=393 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    The information the source conveys either is incorrect or is disputed by an equally authoritative source.
            </td>
        </tr>
        <tr valign=TOP>
            <td width=201 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Ambiguous
            </td>
            <td width=393 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    The transcribed words do not unambiguously support the claim.
            </td>
        </tr>
        <tr valign=TOP>
            <td width=201 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Faithless
            </td>
            <td width=393 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    The source does not faithfully intend to communicate knowledge, but is motivated, on this occasion, by another desire.
            </td>
        </tr>
        <tr valign=TOP>
            <td width=201 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Misstatement
            </td>
            <td width=393 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    The transcribed words that support the claim are the result of a misstatement by the source, or are an inaccurate transcription of the source.
            </td>
        </tr>
    </table>
    <p>

    <h2 id="Perception">Perception</h2></span>

    <p>
        Perception (<i>pratyakṣa</i>) is a way of generating knowledge about something previously uncertain through sensory evidence. As Akṣapāda Gautama writes in <i>Nyāya Sūtra</i> 1.1.4, “Perceptual knowledge arises from connection of a sense faculty and object, does not depend on language, is inerrant, and is definitive” (Dasti and Phillips: 20).
    <p>
        For the purpose of this digital platform, perceptual faculties are limited to the audio and visual senses. Because smelling, tasting, touching, and inner understanding (<i>manas</i>) lack corresponding objects in digital space, there can be no connection of these sense faculties with digital objects. Conversely, contemporary A/V technologies allow hearing and seeing to connect with audible and visible objects in the digital realm. To support claims about smells, tastes, touches and inner awareness, users should instead rely on testimony as a support means. To support claims through audio or visual evidence, users should enter a hyperlink directing users to this digital evidence, and include a timestamp, if appropriate.
    <p>
        The <i>Nyāya Sūtra</i>’s definition of perception stipulates necessary and sufficient conditions that sensory stimulations must satisfy to generate perceptual knowledge. For example, suppose a user wishes to use perception to support the claim, “Cats and dogs sometimes get along.”
    <p>
        The first condition, <b>sense-object contact</b>, requires that the linked content present sights and/or sounds directly of the subject of the claim—that is, “Cats and dogs.” If the linked content features images or sounds that are not of cats and dogs—for example, if the linked content features dogs but not cats, or cartoon renderings of cats and dogs—then the intended support would not satisfy the first criterion of perceptual evidence.
    <p>
        The second condition requires that perceptual evidence <b>does not depend on language</b>(<i>avyapadeśyam</i>). Nyāya philosophers variously interpreted this criterion. For the purposes of this iteration of the Vāda Project platform, the condition requires that all users, regardless of their linguistic backgrounds, would assent to the claim (assuming the proposition were faithfully translated) solely on the basis of the provided sensory evidence. Hindi, Urdu and English speakers could all presumably agree that cats and dogs sometimes get along on the basis of a video of a cat and dog closely resting together.
    <p>
        The second condition further helps to ensure that claims that users intend to support through perception minimize editorializing. The provided sensory evidence should provoke users’ assent to the claim regardless of, not only their linguistic backgrounds, but also their conceptual backgrounds. Reasonable persons can construe “getting along” in different ways. For example, a video of a cat and dog at play could fail to satisfy the second condition if, based on the provided sensory evidence, some users believe the video illustrates one animal abusing the other.
    <p>
        Finally, the second condition also helps to distinguish perception from testimony. Testimony uses words to produce knowledge about a state of affairs. In contrast, perception uses sense faculties to produce knowledge of perceptible objects in ways that do not depend on language. A trustworthy authority might inform us through digital oral testimony that their cat and dog get along. However, perception of this oral evidence may only establish that the person has uttered particular sounds, such as the utterance, “My cat and dog get along.”
    <p>
        The third condition, <b>is inerrant</b>, requires that the perceptual evidence not be false. In the classical literature, this criterion is said to exclude perceptual illusions. In his commentary on <i>Nyāya Sūtra</i>1.1.4, Vātsyāyana writes:
    <p>
        During the summer, the sun rays and the warmth radiating from the hot ground pulsate together and come into sensory connection with the visual organ of a person situated at a distance. In such a situation, the cognition “Water” arises for the observer owing to the connection between his sense organ and the object. So to exclude such false cognition from the definition of perception proper, the author of the sutras includes the qualifier “<b>inerrant</b>.” (Dasti and Phillips: 23).
    <p>
        In contemporary contexts, this third criterion would exclude “deep fakes,” in which A/V evidence is manipulated to establish something that is not the case. It would also exclude perceptual evidence that is sublated by further perceptual evidence. For example, a video of a cat and dog resting together that was submitted to support the claim that “Cats and dogs sometimes get along” would fail to satisfy this criterion if, later in the video, one of the animals wakes in apparent terror of its proximity to the other. Similarly, to use a more pressing example, it would exclude perceptual evidence that has been taken out of context. For example, in 2018, a video circulated through WhatsApp that appeared to show a gang of “child lifters” kidnapping children off streets in India. The video camera captured a real perceptual event and therefore satisfied the first condition of perceptual knowledge. Further, because the video was free of dialogue, it also satisfied the second condition of perceptual knowledge. However, subsequently released perceptual evidence showed that the video “was part of a public service announcement” that sought to warn parents to be vigilant against child abductions.<a class="sdendnoteanc" name="sdendnote3anc" href="#sdendnote3sym"><sup>iii</sup></a>
        Just as further perceptual activity sometimes reveals that a perceived “snake” is a rope, in this case, further perceptual activity revealed that the perceived “child kidnapping” was theater. Conversely, unlike false perceptual cognitions, genuine perceptual knowledge cannot be defeated by later perceptual evidence that itself meets the conditions of perceptual knowledge.
    <p>
        The fourth and final condition, <b>is definitive</b>, requires that the perceptual evidence be unambiguous. In the classical literature, prior to Vācaspatimiśra’s significant reinterpretation of <i>Nyāya Sūtra</i> 1.1.4 in response to technical challenges by Buddhist and Mīmāṃsā philosophers, the fourth criterion was intended to exclude sensory evidence that provides doubtful or uncertain support for perceptual judgment. As Vātsyāyana writes in his commentary on the sūtra, “A person looking at something at a distance is unable to determine precisely what it is, whether it is smoke or a cloud of dust. So to exclude from the ranks of genuine perception such unclear cognition which does arise from a connection between a sense faculty and an object, the sūtra-maker uses the qualifier “<b>definitive</b>” (Dasti and Phillips: 23).
    <p>
        To support a claim through perception, users should select “perception” as a support means for the claim. They will then be prompted to enter a URL that links to the audio/video source in a text box labeled “<b>Enter link to A/V evidence</b>,” and, if applicable, to enter an appropriate timestamp for the linked A/V evidence in a text box labeled “Timestamp for audio/video, if applicable.”
    <p>
        After clicking “submit,” the user will be notified that the claim has been submitted. Upon returning to the debate topic page, users will see that the claim has been added. By clicking on the “details” link on the claim box, users will see that the platform has generated a hyperlink to the audio/video evidence and displays a timestamp if one has been entered.
    <p>
        If they would like, users can now raise doubts about the claim by clicking the “<b>Flag this claim</b>” tab from the “Details” pop-up. The following flags can be raised against a claim supported by perception:
    <table width=623 cellpadding=7 cellspacing=0>
        <col width=201>
        <col width=393>
        <tr valign=TOP>
            <td width=201 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Flag
            </td>
            <td width=393 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Explanation
            </td>
        </tr>
        <tr valign=TOP>
            <td width=201 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    No Sense-Object Contact
            </td>
            <td width=393 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    The linked content does not present sights and/or sounds directly of the subject of the claim.
            </td>
        </tr>
        <tr valign=TOP>
            <td width=201 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Depends on Words
            </td>
            <td width=393 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Either the linked content relies on words to support the claim, or the claim depends on words or concepts that exceed the linked content.
            </td>
        </tr>
        <tr valign=TOP>
            <td width=201 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Errant
            </td>
            <td width=393 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    The linked content presents perceptual evidence that is illusory or false.
            </td>
        </tr>
        <tr valign=TOP>
            <td width=201 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    Ambiguous
            </td>
            <td width=393 style="border: 1px solid #00000a; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0.08in">
                <p>
                    The linked content does not clearly and unambiguously support the claim.
            </td>
        </tr>
    </table>
    <div id="sdendnote1">
        <p>

            <a class="sdendnotesym" name="sdendnote1sym" href="#sdendnote1anc">i</a><sup></sup>
            Matthew Dasti and Stephen Phillips (2017), <i>The Ny</i><i>ā</i><i>ya-s</i><i>ū</i><i>tra: Selections with Early Commentaries</i>, Indianapolis, IN: Hackett Publishing Company, p. 46.

    </div>
    <div id="sdendnote2">
        <p>

            <a class="sdendnotesym" name="sdendnote2sym" href="#sdendnote2anc">ii</a><sup></sup>
            Susan Tawia&nbsp;(1992),&nbsp;“When is the Capacity for Sentience Acquired During Human Fetal Development?”, <i>Journal of Maternal-Fetal Medicine</i>,&nbsp;1:3, 153-165.&nbsp;DOI:&nbsp;<a href="https://doi.org/10.3109/14767059209161911">10.3109/14767059209161911</a>.

    </div>
    <div id="sdendnote3">
        <p>

            <a class="sdendnotesym" name="sdendnote3sym" href="#sdendnote3anc">iii</a><sup></sup>
            Goel, Vindu, Raj, Suhasini, and Ravichandran, Priyadarshini, “How WhatsApp Leads Mobs to Murder in India,” <i>New York Times</i>, July 18, 2018. https://www.nytimes.com/interactive/2018/07/18/technology/whatsapp-india-killings.html. Accessed on September 3, 2019.

    </div>
</main>
<?php include 'includes/page_bottom.php'; ?>
