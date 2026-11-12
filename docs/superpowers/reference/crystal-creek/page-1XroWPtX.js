import { r as e } from './rolldown-runtime-C60lm6uB.js';
import { i as t, r as n } from './framework-D_rUT4EX.js';
var r = (...e) =>
        e
            .filter((e, t, n) => !!e && e.trim() !== `` && n.indexOf(e) === t)
            .join(` `)
            .trim(),
    i = (e) => e.replace(/([a-z0-9])([A-Z])/g, `$1-$2`).toLowerCase(),
    a = (e) =>
        e.replace(/^([A-Z])|[\s-_]+(\w)/g, (e, t, n) =>
            n ? n.toUpperCase() : t.toLowerCase(),
        ),
    o = (e) => {
        let t = a(e);
        return t.charAt(0).toUpperCase() + t.slice(1);
    },
    s = {
        xmlns: `http://www.w3.org/2000/svg`,
        width: 24,
        height: 24,
        viewBox: `0 0 24 24`,
        fill: `none`,
        stroke: `currentColor`,
        strokeWidth: 2,
        strokeLinecap: `round`,
        strokeLinejoin: `round`,
    },
    c = (e) => {
        for (let t in e)
            if (t.startsWith(`aria-`) || t === `role` || t === `title`)
                return !0;
        return !1;
    },
    l = e(t(), 1),
    u = (0, l.createContext)({}),
    d = () => (0, l.useContext)(u),
    f = (0, l.forwardRef)(
        (
            {
                color: e,
                size: t,
                strokeWidth: n,
                absoluteStrokeWidth: i,
                className: a = ``,
                children: o,
                iconNode: u,
                ...f
            },
            p,
        ) => {
            let {
                    size: m = 24,
                    strokeWidth: h = 2,
                    absoluteStrokeWidth: g = !1,
                    color: ee = `currentColor`,
                    className: te = ``,
                } = d() ?? {},
                _ =
                    (i ?? g)
                        ? (Number(n ?? h) * 24) / Number(t ?? m)
                        : (n ?? h);
            return (0, l.createElement)(
                `svg`,
                {
                    ref: p,
                    ...s,
                    width: t ?? m ?? s.width,
                    height: t ?? m ?? s.height,
                    stroke: e ?? ee,
                    strokeWidth: _,
                    className: r(`lucide`, te, a),
                    ...(!o && !c(f) && { 'aria-hidden': `true` }),
                    ...f,
                },
                [
                    ...u.map(([e, t]) => (0, l.createElement)(e, t)),
                    ...(Array.isArray(o) ? o : [o]),
                ],
            );
        },
    ),
    p = (e, t) => {
        let n = (0, l.forwardRef)(({ className: n, ...a }, s) =>
            (0, l.createElement)(f, {
                ref: s,
                iconNode: t,
                className: r(`lucide-${i(o(e))}`, `lucide-${e}`, n),
                ...a,
            }),
        );
        return ((n.displayName = o(e)), n);
    },
    m = p(`arrow-down-right`, [
        [`path`, { d: `m7 7 10 10`, key: `1fmybs` }],
        [`path`, { d: `M17 7v10H7`, key: `6fjiku` }],
    ]),
    h = p(`arrow-left`, [
        [`path`, { d: `m12 19-7-7 7-7`, key: `1l729n` }],
        [`path`, { d: `M19 12H5`, key: `x3x0zl` }],
    ]),
    g = p(`arrow-right`, [
        [`path`, { d: `M5 12h14`, key: `1ays0h` }],
        [`path`, { d: `m12 5 7 7-7 7`, key: `xquz4c` }],
    ]),
    ee = p(`arrow-up-right`, [
        [`path`, { d: `M7 7h10v10`, key: `1tivn9` }],
        [`path`, { d: `M7 17 17 7`, key: `1vkiza` }],
    ]),
    te = p(`bug`, [
        [`path`, { d: `M12 20v-9`, key: `1qisl0` }],
        [
            `path`,
            {
                d: `M14 7a4 4 0 0 1 4 4v3a6 6 0 0 1-12 0v-3a4 4 0 0 1 4-4z`,
                key: `uouzyp`,
            },
        ],
        [`path`, { d: `M14.12 3.88 16 2`, key: `qol33r` }],
        [`path`, { d: `M21 21a4 4 0 0 0-3.81-4`, key: `1b0z45` }],
        [`path`, { d: `M21 5a4 4 0 0 1-3.55 3.97`, key: `5cxbf6` }],
        [`path`, { d: `M22 13h-4`, key: `1jl80f` }],
        [`path`, { d: `M3 21a4 4 0 0 1 3.81-4`, key: `1fjd4g` }],
        [`path`, { d: `M3 5a4 4 0 0 0 3.55 3.97`, key: `1d7oge` }],
        [`path`, { d: `M6 13H2`, key: `82j7cp` }],
        [`path`, { d: `m8 2 1.88 1.88`, key: `fmnt4t` }],
        [`path`, { d: `M9 7.13V6a3 3 0 1 1 6 0v1.13`, key: `1vgav8` }],
    ]),
    _ = p(`calculator`, [
        [
            `rect`,
            {
                width: `16`,
                height: `20`,
                x: `4`,
                y: `2`,
                rx: `2`,
                key: `1nb95v`,
            },
        ],
        [`line`, { x1: `8`, x2: `16`, y1: `6`, y2: `6`, key: `x4nwl0` }],
        [`line`, { x1: `16`, x2: `16`, y1: `14`, y2: `18`, key: `wjye3r` }],
        [`path`, { d: `M16 10h.01`, key: `1m94wz` }],
        [`path`, { d: `M12 10h.01`, key: `1nrarc` }],
        [`path`, { d: `M8 10h.01`, key: `19clt8` }],
        [`path`, { d: `M12 14h.01`, key: `1etili` }],
        [`path`, { d: `M8 14h.01`, key: `6423bh` }],
        [`path`, { d: `M12 18h.01`, key: `mhygvu` }],
        [`path`, { d: `M8 18h.01`, key: `lrp35t` }],
    ]),
    v = p(`check`, [[`path`, { d: `M20 6 9 17l-5-5`, key: `1gmf2c` }]]),
    ne = p(`chevron-right`, [[`path`, { d: `m9 18 6-6-6-6`, key: `mthhwq` }]]),
    y = p(`clipboard-list`, [
        [
            `rect`,
            {
                width: `8`,
                height: `4`,
                x: `8`,
                y: `2`,
                rx: `1`,
                ry: `1`,
                key: `tgr4d6`,
            },
        ],
        [
            `path`,
            {
                d: `M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2`,
                key: `116196`,
            },
        ],
        [`path`, { d: `M12 11h4`, key: `1jrz19` }],
        [`path`, { d: `M12 16h4`, key: `n85exb` }],
        [`path`, { d: `M8 11h.01`, key: `1dfujw` }],
        [`path`, { d: `M8 16h.01`, key: `18s6g9` }],
    ]),
    re = p(`clock-3`, [
        [`circle`, { cx: `12`, cy: `12`, r: `10`, key: `1mglay` }],
        [`path`, { d: `M12 6v6h4`, key: `135r8i` }],
    ]),
    ie = p(`file-text`, [
        [
            `path`,
            {
                d: `M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z`,
                key: `1oefj6`,
            },
        ],
        [`path`, { d: `M14 2v5a1 1 0 0 0 1 1h5`, key: `wfsgrz` }],
        [`path`, { d: `M10 9H8`, key: `b1mrlr` }],
        [`path`, { d: `M16 13H8`, key: `t4e002` }],
        [`path`, { d: `M16 17H8`, key: `z1uh3a` }],
    ]),
    ae = p(`git-compare-arrows`, [
        [`circle`, { cx: `5`, cy: `6`, r: `3`, key: `1qnov2` }],
        [`path`, { d: `M12 6h5a2 2 0 0 1 2 2v7`, key: `1yj91y` }],
        [`path`, { d: `m15 9-3-3 3-3`, key: `1lwv8l` }],
        [`circle`, { cx: `19`, cy: `18`, r: `3`, key: `1qljk2` }],
        [`path`, { d: `M12 18H7a2 2 0 0 1-2-2V9`, key: `16sdep` }],
        [`path`, { d: `m9 15 3 3-3 3`, key: `1m3kbl` }],
    ]),
    b = p(`house`, [
        [
            `path`,
            { d: `M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8`, key: `5wwlr5` },
        ],
        [
            `path`,
            {
                d: `M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z`,
                key: `r6nss1`,
            },
        ],
    ]),
    oe = p(`layout-grid`, [
        [
            `rect`,
            { width: `7`, height: `7`, x: `3`, y: `3`, rx: `1`, key: `1g98yp` },
        ],
        [
            `rect`,
            {
                width: `7`,
                height: `7`,
                x: `14`,
                y: `3`,
                rx: `1`,
                key: `6d4xhi`,
            },
        ],
        [
            `rect`,
            {
                width: `7`,
                height: `7`,
                x: `14`,
                y: `14`,
                rx: `1`,
                key: `nxv5o0`,
            },
        ],
        [
            `rect`,
            {
                width: `7`,
                height: `7`,
                x: `3`,
                y: `14`,
                rx: `1`,
                key: `1bb6yr`,
            },
        ],
    ]),
    se = p(`menu`, [
        [`path`, { d: `M4 5h16`, key: `1tepv9` }],
        [`path`, { d: `M4 12h16`, key: `1lakjw` }],
        [`path`, { d: `M4 19h16`, key: `1djgab` }],
    ]),
    x = p(`plus`, [
        [`path`, { d: `M5 12h14`, key: `1ays0h` }],
        [`path`, { d: `M12 5v14`, key: `s699le` }],
    ]),
    S = p(`printer`, [
        [
            `path`,
            {
                d: `M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2`,
                key: `143wyd`,
            },
        ],
        [
            `path`,
            { d: `M6 9V3a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6`, key: `1itne7` },
        ],
        [
            `rect`,
            {
                x: `6`,
                y: `14`,
                width: `12`,
                height: `8`,
                rx: `1`,
                key: `1ue0tg`,
            },
        ],
    ]),
    ce = p(`search`, [
        [`path`, { d: `m21 21-4.34-4.34`, key: `14j7rj` }],
        [`circle`, { cx: `11`, cy: `11`, r: `8`, key: `4ej97u` }],
    ]),
    C = p(`settings-2`, [
        [`path`, { d: `M14 17H5`, key: `gfn3mx` }],
        [`path`, { d: `M19 7h-9`, key: `6i9tg` }],
        [`circle`, { cx: `17`, cy: `17`, r: `3`, key: `18b49y` }],
        [`circle`, { cx: `7`, cy: `7`, r: `3`, key: `dfmy0x` }],
    ]),
    le = p(`sliders-horizontal`, [
        [`path`, { d: `M10 5H3`, key: `1qgfaw` }],
        [`path`, { d: `M12 19H3`, key: `yhmn1j` }],
        [`path`, { d: `M14 3v4`, key: `1sua03` }],
        [`path`, { d: `M16 17v4`, key: `1q0r14` }],
        [`path`, { d: `M21 12h-9`, key: `1o4lsq` }],
        [`path`, { d: `M21 19h-5`, key: `1rlt1p` }],
        [`path`, { d: `M21 5h-7`, key: `1oszz2` }],
        [`path`, { d: `M8 10v4`, key: `tgpxqk` }],
        [`path`, { d: `M8 12H3`, key: `a7s4jb` }],
    ]),
    w = p(`trash-2`, [
        [`path`, { d: `M10 11v6`, key: `nco0om` }],
        [`path`, { d: `M14 11v6`, key: `outv1u` }],
        [
            `path`,
            { d: `M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6`, key: `miytrc` },
        ],
        [`path`, { d: `M3 6h18`, key: `d0wm0j` }],
        [
            `path`,
            { d: `M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2`, key: `e791ji` },
        ],
    ]),
    T = p(`wind`, [
        [`path`, { d: `M12.8 19.6A2 2 0 1 0 14 16H2`, key: `148xed` }],
        [`path`, { d: `M17.5 8a2.5 2.5 0 1 1 2 4H2`, key: `1u4tom` }],
        [`path`, { d: `M9.8 4.4A2 2 0 1 1 11 8H2`, key: `75valh` }],
    ]),
    ue = p(`wrench`, [
        [
            `path`,
            {
                d: `M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.106-3.105c.32-.322.863-.22.983.218a6 6 0 0 1-8.259 7.057l-7.91 7.91a1 1 0 0 1-2.999-3l7.91-7.91a6 6 0 0 1 7.057-8.259c.438.12.54.662.219.984z`,
                key: `1ngwbx`,
            },
        ],
    ]),
    de = p(`x`, [
        [`path`, { d: `M18 6 6 18`, key: `1bl5f8` }],
        [`path`, { d: `m6 6 12 12`, key: `d8bk6v` }],
    ]),
    E = [
        {
            name: `Crystal Creek Air`,
            short: `Air`,
            icon: `wind`,
            color: `#287f9d`,
            tone: `#e9f4f8`,
        },
        {
            name: `Crystal Creek Pest Control`,
            short: `Pest Control`,
            icon: `bug`,
            color: `#39835a`,
            tone: `#eaf4ed`,
        },
        {
            name: `Crystal Creek Roofing`,
            short: `Roofing`,
            icon: `roof`,
            color: `#9f6336`,
            tone: `#fbf0e4`,
        },
        {
            name: `Texas Home Maintenance Pros`,
            short: `Maintenance`,
            icon: `tools`,
            color: `#62579b`,
            tone: `#efedf8`,
        },
    ],
    D = [
        `HVAC repair`,
        `HVAC replacement`,
        `Pest plan`,
        `Pest treatment`,
        `Roofing`,
        `Siding`,
        `Make-ready`,
        `Cleaning`,
        `Plumbing`,
        `General maintenance`,
    ],
    fe = [`Draft`, `Pending approval`, `Approved`, `Declined`],
    O = {
        markup: 30,
        discount: 0,
        trip: 0,
        authorization: 250,
        manager: 1e3,
        target: 25,
    },
    k = (e = ``, t = `job`, n = null) => ({
        name: e,
        qty: 1,
        unit: t,
        labor: 0,
        materials: 0,
        fixed: n,
    }),
    A = (e, t, n, r, i, a, o = ``) => ({
        id: e,
        title: t,
        company: n,
        service: D[r],
        scope: i,
        lines: a,
        terms: o,
    }),
    j = [
        A(
            `t1`,
            `Evaporator coil replacement`,
            0,
            0,
            `Replace the evaporator coil, pressure test, evacuate, and verify cooling operation.`,
            [
                k(`Evaporator coil labor`, `job`, 1800),
                k(`Coil or warranty allowance`),
                k(`Refrigerant`, `lb`),
                k(`Access or carpentry`),
            ],
            `Confirm warranty eligibility and access requirements. Price refrigerant and access separately when applicable.`,
        ),
        A(
            `t2`,
            `Condenser coil replacement`,
            0,
            0,
            `Replace the condenser coil, leak test, evacuate, recharge and test operation.`,
            [
                k(`Condenser coil labor`, `job`, 1500),
                k(`Coil or warranty allowance`),
                k(`Refrigerant`, `lb`),
            ],
        ),
        A(
            `t3`,
            `Complete HVAC system replacement`,
            0,
            1,
            `Remove existing equipment, install matched replacement equipment, and commission the system.`,
            [
                k(`Matched condenser and indoor equipment`),
                k(`Installation labor`, `hr`),
                k(`Drain, float switch and connection materials`),
                k(`Disposal and startup`),
            ],
            `Confirm system size, equipment model, permit requirements and warranty.`,
        ),
        ...[`Essential`, `Plus`, `Premier`].map((e, t) =>
            A(
                `p` + t,
                e + ` quarterly pest plan`,
                1,
                2,
                `Initial general pest service for the selected quarterly plan. Confirm covered pests and service areas before issuing.`,
                [
                    k(
                        `Initial service · quarterly enrollment promo`,
                        `visit`,
                        150,
                    ),
                ],
                `This total covers the initial service only. Enter the recurring quarterly fee and service terms here before issuing. Additional treatment discount for this tier is ` +
                    [10, 15, 20][t] +
                    `%.`,
            ),
        ),
        A(
            `p3`,
            `German cockroach treatment`,
            1,
            3,
            `Inspect and treat affected areas. Confirm infestation severity and preparation requirements.`,
            [k(`Starting treatment price`, `job`, 550)],
            `Starting rate. Confirm follow-up frequency and the final scope after inspection.`,
        ),
        A(
            `p4`,
            `Pest add-on treatment menu`,
            1,
            3,
            `Select the requested treatments. Remove unused rows or set their quantity to zero.`,
            [
                [`Mosquito`, 75],
                [`Rodent inspection and trapping`, 200],
                [`Flea and tick`, 350],
                [`Wasp or hornet`, 150],
                [`Fire ant yard treatment`, 150],
                [`Bed bug treatment per room`, 350],
                [`Carpenter ant`, 200],
                [`Carpenter bee`, 175],
            ].map(([e, t]) => ({
                ...k(
                    String(e),
                    e === `Bed bug treatment per room` ? `room` : `visit`,
                    Number(t),
                ),
                qty: 0,
            })),
        ),
        A(
            `r1`,
            `Roof replacement scope`,
            2,
            4,
            `Tear off and dispose of existing roofing. Install specified underlayment, shingles, flashing and ventilation. Clean up and magnet-sweep the site.`,
            [
                k(`Tear-off and installation`, `sq`),
                k(`Shingles and waste allowance`, `sq`),
                k(`Underlayment and starter`, `sq`),
                k(`Drip edge and flashing`, `lf`),
                k(`Decking allowance`, `sheet`),
                k(`Permit or inspection allowance`),
            ],
            `Identify decking allowance and any applicable windstorm inspection requirements before approval.`,
        ),
        A(
            `m1`,
            `Hardie lap siding replacement`,
            3,
            5,
            `Remove designated siding, inspect exposed sheathing, install house wrap and flashing, and install primed lap siding and trim.`,
            [
                k(`Tear-off and installation`, `sq ft`),
                k(`Fiber cement siding`, `sq ft`),
                k(`House wrap and flashing`, `sq ft`),
                k(`Exterior trim`, `lf`),
                k(`Debris haul-off`),
            ],
            `Painting and concealed rot repairs are excluded unless itemized.`,
        ),
        A(
            `m2`,
            `Rental make-ready`,
            3,
            6,
            `Complete approved turnover repairs. Confirm the work list from the move-out inspection and document completion.`,
            [
                k(`Patch and paint`, `room`),
                k(`Hardware and fixture repairs`),
                k(`Turnover cleaning`),
                k(`Trash-out and disposal`),
                k(`Final walkthrough`),
            ],
        ),
        A(
            `m3`,
            `Move-out or restoration cleaning`,
            3,
            7,
            `Clean agreed rooms, fixtures and surfaces. Document heavy soil or restoration needs separately.`,
            [
                k(`Standard turnover cleaning`, `job`, 310),
                k(`Restoration cleaning add-on`, `hr`),
                k(`Supplies and disposal`),
            ],
        ),
        A(
            `m4`,
            `Plumbing repair`,
            3,
            8,
            `Identify the source, complete the approved repair and test for leaks and normal operation.`,
            [
                k(`Diagnostic or repair labor`, `hr`),
                k(`Fixture or replacement parts`),
                k(`Access and restoration allowance`),
            ],
        ),
        A(
            `m5`,
            `General maintenance service`,
            3,
            9,
            `Complete the listed service tasks and document the work performed.`,
            [
                k(`Technician labor`, `hr`),
                k(`Repair materials`),
                k(`Disposal or equipment allowance`),
            ],
        ),
    ],
    M = (e) =>
        new Intl.NumberFormat(`en-US`, {
            style: `currency`,
            currency: `USD`,
        }).format(e),
    N = (e) => Math.round((e + 2 ** -52) * 100) / 100,
    P = (e, t) =>
        N(e.qty * (e.fixed ?? (e.labor + e.materials) * (1 + t / 100)));
function F(e) {
    let { lines: t, pricing: n } = e,
        r = N(t.reduce((e, t) => e + N(t.qty * (t.labor + t.materials)), 0)),
        i = N(t.reduce((e, t) => e + P(t, n.markup), 0)),
        a = N((i * n.discount) / 100),
        o = N(i - a + n.trip);
    return {
        cost: r,
        subtotal: i,
        discount: a,
        total: o,
        incomplete: t.some((e) => e.qty > 0 && e.labor + e.materials === 0),
        margin: o > 0 ? ((o - r) / o) * 100 : 0,
    };
}
var I = (e) =>
        new Intl.DateTimeFormat(`en-US`, {
            month: `short`,
            day: `numeric`,
            year: `numeric`,
            timeZone: `America/Chicago`,
        }).format(new Date(e)),
    pe = { defaults: O, templates: j, estimates: [] },
    L = n(),
    R = [T, te, b, ue],
    me = [
        { name: `Estimates`, Icon: y },
        { name: `Service templates`, Icon: oe },
        { name: `Compare estimates`, Icon: ae },
        { name: `Calculator`, Icon: _ },
        { name: `Pricing defaults`, Icon: C },
    ],
    z = [
        [`markup`, `Cost markup`, `%`],
        [`discount`, `Subtotal discount`, `%`],
        [`trip`, `Trip charge`, `$`],
        [`authorization`, `Authorization limit`, `$`],
        [`manager`, `Manager review threshold`, `$`],
        [`target`, `Target gross margin`, `%`],
    ],
    B = (e) => ({ '--company-tone': E[e].tone, '--company-color': E[e].color });
function V({ i: e }) {
    let t = R[e];
    return (0, L.jsx)(`span`, {
        className: `brand-icon`,
        style: B(e),
        children: (0, L.jsx)(t, { size: 19, strokeWidth: 1.8 }),
    });
}
function H({ status: e }) {
    return (0, L.jsxs)(`span`, {
        className: `badge ` + e.toLowerCase().replaceAll(` `, `-`),
        children: [(0, L.jsx)(`i`, {}), e],
    });
}
function U({ value: e, change: t, max: n, label: r }) {
    return (0, L.jsx)(`input`, {
        type: `number`,
        min: `0`,
        max: n,
        step: `any`,
        'aria-label': r,
        value: e ?? ``,
        onChange: (e) => {
            let r = e.target.value;
            t(
                r === ``
                    ? null
                    : Math.min(n ?? 1e8, Math.max(0, Number(r) || 0)),
            );
        },
    });
}
function he({ value: e, change: t }) {
    return (0, L.jsx)(`div`, {
        className: `fields pricing-fields`,
        children: z.map(([n, r, i]) =>
            (0, L.jsxs)(
                `label`,
                {
                    children: [
                        r,
                        (0, L.jsxs)(`div`, {
                            className: `unit-input`,
                            children: [
                                (0, L.jsx)(`span`, { children: i }),
                                (0, L.jsx)(U, {
                                    label: r,
                                    value: e[n],
                                    max:
                                        n === `discount` || n === `target`
                                            ? 100
                                            : void 0,
                                    change: (r) => t({ ...e, [n]: r ?? 0 }),
                                }),
                            ],
                        }),
                    ],
                },
                n,
            ),
        ),
    });
}
function ge({ value: e, change: t, markup: n }) {
    let r = (n, r) => t(e.map((e, t) => (t === n ? { ...e, ...r } : e)));
    return (0, L.jsxs)(L.Fragment, {
        children: [
            (0, L.jsx)(`div`, {
                className: `scroll`,
                children: (0, L.jsxs)(`table`, {
                    className: `line-table`,
                    children: [
                        (0, L.jsx)(`thead`, {
                            children: (0, L.jsxs)(`tr`, {
                                children: [
                                    (0, L.jsx)(`th`, { children: `Item` }),
                                    (0, L.jsx)(`th`, { children: `Qty` }),
                                    (0, L.jsx)(`th`, { children: `Unit` }),
                                    (0, L.jsx)(`th`, {
                                        children: `Labor / unit`,
                                    }),
                                    (0, L.jsx)(`th`, {
                                        children: `Materials / unit`,
                                    }),
                                    (0, L.jsx)(`th`, {
                                        children: `Fixed sell / unit`,
                                    }),
                                    n !== void 0 &&
                                        (0, L.jsx)(`th`, {
                                            children: `Sell total`,
                                        }),
                                    (0, L.jsx)(`th`, {}),
                                ],
                            }),
                        }),
                        (0, L.jsx)(`tbody`, {
                            children: e.map((i, a) =>
                                (0, L.jsxs)(
                                    `tr`,
                                    {
                                        children: [
                                            (0, L.jsx)(`td`, {
                                                children: (0, L.jsx)(`input`, {
                                                    'aria-label': `Item ${a + 1}`,
                                                    placeholder: `Work or material`,
                                                    value: i.name,
                                                    onChange: (e) =>
                                                        r(a, {
                                                            name: e.target
                                                                .value,
                                                        }),
                                                }),
                                            }),
                                            (0, L.jsx)(`td`, {
                                                children: (0, L.jsx)(U, {
                                                    label: `Quantity ${a + 1}`,
                                                    value: i.qty,
                                                    change: (e) =>
                                                        r(a, { qty: e ?? 0 }),
                                                }),
                                            }),
                                            (0, L.jsx)(`td`, {
                                                children: (0, L.jsx)(`input`, {
                                                    'aria-label': `Unit ${a + 1}`,
                                                    value: i.unit,
                                                    onChange: (e) =>
                                                        r(a, {
                                                            unit: e.target
                                                                .value,
                                                        }),
                                                }),
                                            }),
                                            (0, L.jsx)(`td`, {
                                                children: (0, L.jsx)(U, {
                                                    label: `Labor ${a + 1}`,
                                                    value: i.labor,
                                                    change: (e) =>
                                                        r(a, { labor: e ?? 0 }),
                                                }),
                                            }),
                                            (0, L.jsx)(`td`, {
                                                children: (0, L.jsx)(U, {
                                                    label: `Materials ${a + 1}`,
                                                    value: i.materials,
                                                    change: (e) =>
                                                        r(a, {
                                                            materials: e ?? 0,
                                                        }),
                                                }),
                                            }),
                                            (0, L.jsx)(`td`, {
                                                children: (0, L.jsx)(U, {
                                                    label: `Fixed sell price ${a + 1}`,
                                                    value: i.fixed,
                                                    change: (e) =>
                                                        r(a, { fixed: e }),
                                                }),
                                            }),
                                            n !== void 0 &&
                                                (0, L.jsx)(`td`, {
                                                    children: M(P(i, n)),
                                                }),
                                            (0, L.jsx)(`td`, {
                                                children: (0, L.jsx)(`button`, {
                                                    title: `Remove line`,
                                                    className: `icon-button`,
                                                    onClick: () =>
                                                        t(
                                                            e.filter(
                                                                (e, t) =>
                                                                    t !== a,
                                                            ),
                                                        ),
                                                    children: (0, L.jsx)(w, {
                                                        size: 15,
                                                    }),
                                                }),
                                            }),
                                        ],
                                    },
                                    a,
                                ),
                            ),
                        }),
                    ],
                }),
            }),
            (0, L.jsxs)(`button`, {
                className: `button secondary compact`,
                onClick: () => t([...e, k()]),
                children: [(0, L.jsx)(x, { size: 15 }), ` Add line`],
            }),
        ],
    });
}
function _e({ e }) {
    let t = F(e),
        n = !t.total || t.incomplete ? `—` : t.margin.toFixed(1) + `%`;
    return (0, L.jsxs)(`aside`, {
        className: `panel total-panel`,
        children: [
            (0, L.jsx)(`h3`, { children: `Pricing summary` }),
            (0, L.jsxs)(`dl`, {
                children: [
                    (0, L.jsx)(`dt`, { children: `Direct cost` }),
                    (0, L.jsx)(`dd`, { children: M(t.cost) }),
                    (0, L.jsx)(`dt`, { children: `Line subtotal` }),
                    (0, L.jsx)(`dd`, { children: M(t.subtotal) }),
                    (0, L.jsx)(`dt`, { children: `Discount` }),
                    (0, L.jsxs)(`dd`, { children: [`−`, M(t.discount)] }),
                    (0, L.jsx)(`dt`, { children: `Trip charge` }),
                    (0, L.jsx)(`dd`, { children: M(e.pricing.trip) }),
                ],
            }),
            (0, L.jsxs)(`div`, {
                className: `total-row`,
                children: [
                    (0, L.jsx)(`span`, { children: `Estimate total` }),
                    (0, L.jsx)(`strong`, { children: M(t.total) }),
                ],
            }),
            (0, L.jsxs)(`div`, {
                className: `margin-row`,
                children: [
                    (0, L.jsx)(`span`, { children: `Gross margin` }),
                    (0, L.jsx)(`strong`, { children: n }),
                ],
            }),
            (0, L.jsxs)(`p`, {
                children: [
                    `Target `,
                    e.pricing.target,
                    `%`,
                    !t.incomplete && t.total > 0 && t.margin < e.pricing.target
                        ? ` · Below target`
                        : ``,
                ],
            }),
            t.incomplete &&
                (0, L.jsx)(`p`, {
                    className: `attention`,
                    children: `Enter direct costs for active lines to calculate margin. Check any zero-priced scope items before issuing.`,
                }),
            t.total > e.pricing.authorization &&
                (0, L.jsxs)(`p`, {
                    className: `attention`,
                    children: [
                        `Above the `,
                        M(e.pricing.authorization),
                        ` authorization limit.`,
                    ],
                }),
            t.total > e.pricing.manager &&
                (0, L.jsx)(`p`, {
                    className: `attention`,
                    children: `Manager review threshold exceeded.`,
                }),
            (0, L.jsx)(`p`, {
                children: `Authorization flags are advisory. Margin is before overhead and tax.`,
            }),
        ],
    });
}
function ve({ e, audience: t, setAudience: n, close: r }) {
    let i = F(e);
    return (0, L.jsxs)(`div`, {
        className: `proposal-wrap`,
        children: [
            (0, L.jsxs)(`div`, {
                className: `proposal-tools`,
                children: [
                    (0, L.jsxs)(`button`, {
                        className: `button secondary`,
                        onClick: r,
                        children: [
                            (0, L.jsx)(h, { size: 16 }),
                            ` Back to estimate`,
                        ],
                    }),
                    (0, L.jsxs)(`label`, {
                        children: [
                            `Audience `,
                            (0, L.jsxs)(`select`, {
                                value: t,
                                onChange: (e) => n(e.target.value),
                                children: [
                                    (0, L.jsx)(`option`, {
                                        children: `Customer`,
                                    }),
                                    (0, L.jsx)(`option`, {
                                        children: `Property owner`,
                                    }),
                                ],
                            }),
                        ],
                    }),
                    (0, L.jsxs)(`button`, {
                        className: `button primary`,
                        onClick: () => window.print(),
                        children: [
                            (0, L.jsx)(S, { size: 16 }),
                            ` Print / Save PDF`,
                        ],
                    }),
                ],
            }),
            (0, L.jsxs)(`article`, {
                className: `proposal`,
                children: [
                    (0, L.jsxs)(`div`, {
                        className: `proposal-top`,
                        children: [
                            (0, L.jsx)(V, { i: e.company }),
                            (0, L.jsxs)(`div`, {
                                children: [
                                    (0, L.jsx)(`strong`, {
                                        children: E[e.company].name,
                                    }),
                                    (0, L.jsx)(`p`, {
                                        children: `Greater Houston, Texas`,
                                    }),
                                ],
                            }),
                        ],
                    }),
                    (0, L.jsxs)(`div`, {
                        className: `proposal-title`,
                        children: [
                            (0, L.jsxs)(`div`, {
                                children: [
                                    (0, L.jsxs)(`span`, {
                                        className: `eyebrow`,
                                        children: [t, ` proposal`],
                                    }),
                                    (0, L.jsx)(`h1`, {
                                        children: e.title || `Service proposal`,
                                    }),
                                ],
                            }),
                            (0, L.jsxs)(`div`, {
                                children: [
                                    e.number,
                                    (0, L.jsx)(`br`, {}),
                                    I(e.updated),
                                ],
                            }),
                        ],
                    }),
                    (0, L.jsxs)(`div`, {
                        className: `proposal-client`,
                        children: [
                            (0, L.jsx)(`strong`, {
                                children: e.client || `Prepared for`,
                            }),
                            (0, L.jsx)(`br`, {}),
                            e.address || `Service address`,
                            (0, L.jsx)(`br`, {}),
                            e.service,
                        ],
                    }),
                    e.scope &&
                        (0, L.jsxs)(`section`, {
                            children: [
                                (0, L.jsx)(`h3`, { children: `Scope of work` }),
                                (0, L.jsx)(`p`, {
                                    className: `multiline`,
                                    children: e.scope,
                                }),
                            ],
                        }),
                    (0, L.jsxs)(`section`, {
                        children: [
                            (0, L.jsx)(`h3`, { children: `Proposed pricing` }),
                            (0, L.jsxs)(`table`, {
                                children: [
                                    (0, L.jsx)(`thead`, {
                                        children: (0, L.jsxs)(`tr`, {
                                            children: [
                                                (0, L.jsx)(`th`, {
                                                    children: `Service item`,
                                                }),
                                                (0, L.jsx)(`th`, {
                                                    children: `Quantity`,
                                                }),
                                                (0, L.jsx)(`th`, {
                                                    children: `Amount`,
                                                }),
                                            ],
                                        }),
                                    }),
                                    (0, L.jsxs)(`tbody`, {
                                        children: [
                                            e.lines
                                                .filter((e) => e.qty > 0)
                                                .map((t, n) =>
                                                    (0, L.jsxs)(
                                                        `tr`,
                                                        {
                                                            children: [
                                                                (0, L.jsx)(
                                                                    `td`,
                                                                    {
                                                                        children:
                                                                            t.name ||
                                                                            `Service item`,
                                                                    },
                                                                ),
                                                                (0, L.jsxs)(
                                                                    `td`,
                                                                    {
                                                                        children:
                                                                            [
                                                                                t.qty,
                                                                                ` `,
                                                                                t.unit,
                                                                            ],
                                                                    },
                                                                ),
                                                                (0, L.jsx)(
                                                                    `td`,
                                                                    {
                                                                        children:
                                                                            M(
                                                                                P(
                                                                                    t,
                                                                                    e
                                                                                        .pricing
                                                                                        .markup,
                                                                                ),
                                                                            ),
                                                                    },
                                                                ),
                                                            ],
                                                        },
                                                        n,
                                                    ),
                                                ),
                                            i.discount > 0 &&
                                                (0, L.jsxs)(`tr`, {
                                                    children: [
                                                        (0, L.jsx)(`td`, {
                                                            children: `Discount`,
                                                        }),
                                                        (0, L.jsx)(`td`, {}),
                                                        (0, L.jsxs)(`td`, {
                                                            children: [
                                                                `−`,
                                                                M(i.discount),
                                                            ],
                                                        }),
                                                    ],
                                                }),
                                            e.pricing.trip > 0 &&
                                                (0, L.jsxs)(`tr`, {
                                                    children: [
                                                        (0, L.jsx)(`td`, {
                                                            children: `Trip charge`,
                                                        }),
                                                        (0, L.jsx)(`td`, {}),
                                                        (0, L.jsx)(`td`, {
                                                            children: M(
                                                                e.pricing.trip,
                                                            ),
                                                        }),
                                                    ],
                                                }),
                                        ],
                                    }),
                                ],
                            }),
                            (0, L.jsxs)(`div`, {
                                className: `total-row`,
                                children: [
                                    (0, L.jsx)(`span`, {
                                        children: `Proposed total`,
                                    }),
                                    (0, L.jsx)(`strong`, {
                                        children: M(i.total),
                                    }),
                                ],
                            }),
                        ],
                    }),
                    e.terms &&
                        (0, L.jsxs)(`section`, {
                            children: [
                                (0, L.jsx)(`h3`, {
                                    children: `Terms and exclusions`,
                                }),
                                (0, L.jsx)(`p`, {
                                    className: `multiline`,
                                    children: e.terms,
                                }),
                            ],
                        }),
                    (0, L.jsxs)(`div`, {
                        className: `signature`,
                        children: [
                            (0, L.jsxs)(`span`, {
                                children: [t, ` authorization`],
                            }),
                            (0, L.jsx)(`span`, { children: `Date` }),
                        ],
                    }),
                ],
            }),
        ],
    });
}
function ye() {
    let [e, t] = (0, l.useState)(pe),
        [n, r] = (0, l.useState)(0),
        [i, a] = (0, l.useState)(!1),
        [o, s] = (0, l.useState)(!1),
        [c, u] = (0, l.useState)(``),
        [d, f] = (0, l.useState)(``),
        [p, te] = (0, l.useState)(`Estimates`),
        [_, y] = (0, l.useState)(-1),
        [b, S] = (0, l.useState)(``),
        [C, w] = (0, l.useState)(``),
        [T, ue] = (0, l.useState)(``),
        [O, A] = (0, l.useState)(null),
        [j, P] = (0, l.useState)(null),
        [R, z] = (0, l.useState)(pe.defaults),
        [ye, W] = (0, l.useState)(!1),
        [be, xe] = (0, l.useState)(`Property owner`),
        [Se, G] = (0, l.useState)(!1),
        [K, Ce] = (0, l.useState)([]),
        [q, J] = (0, l.useState)({ qty: 1, price: 0, extra: 0 }),
        [we, Te] = (0, l.useState)(!1);
    (0, l.useEffect)(() => {
        fetch(`/api/workspace`, { cache: `no-store` })
            .then(async (e) => {
                if (!e.ok)
                    throw Error(
                        `The estimating workspace could not load. Reload to try again.`,
                    );
                let n = await e.json();
                (r(n.version),
                    n.data && (t(n.data), z(n.data.defaults)),
                    a(!0));
            })
            .catch((e) => f(e.message));
    }, []);
    async function Ee(e, i) {
        (s(!0), f(``), u(``));
        try {
            let a = await fetch(`/api/workspace`, {
                method: `PUT`,
                headers: { 'Content-Type': `application/json` },
                body: JSON.stringify({ version: n, data: e }),
            });
            if (!a.ok)
                throw Error(
                    a.status === 409 || a.status === 413
                        ? await a.text()
                        : `The workspace could not save. Your edits are still open.`,
                );
            return (r((await a.json()).version), t(e), u(i), !0);
        } catch (e) {
            return (f(e instanceof Error ? e.message : `Save failed.`), !1);
        } finally {
            s(!1);
        }
    }
    function Y(t, n = -1) {
        (te(t),
            y(n),
            S(``),
            w(``),
            ue(``),
            A(null),
            P(null),
            G(!1),
            W(!1),
            Te(!1),
            u(``),
            z(e.defaults),
            window.scrollTo(0, 0));
    }
    function De(t, n) {
        let r = crypto.randomUUID(),
            i = new Date().toISOString(),
            a = t?.company ?? n?.company ?? Math.max(0, _);
        (A(
            n
                ? {
                      ...n,
                      title: n.title + ` (copy)`,
                      id: r,
                      number: `EST-` + r.slice(0, 6).toUpperCase(),
                      date: i,
                      updated: i,
                      status: `Draft`,
                  }
                : {
                      id: r,
                      number: `EST-` + r.slice(0, 6).toUpperCase(),
                      title: t?.title ?? ``,
                      company: a,
                      service: t?.service ?? (b || D[[0, 2, 4, 9][a]]),
                      client: ``,
                      address: ``,
                      status: `Draft`,
                      notes: ``,
                      terms: t?.terms ?? ``,
                      scope: t?.scope ?? ``,
                      date: i,
                      updated: i,
                      pricing: { ...e.defaults },
                      lines: t ? structuredClone(t.lines) : [k()],
                  },
        ),
            P(null),
            G(!1),
            W(!1),
            u(``),
            window.scrollTo(0, 0));
    }
    function Oe(e) {
        let { history: t, ...n } = e;
        (A(structuredClone(n)), G(!1), W(!1), u(``), window.scrollTo(0, 0));
    }
    async function ke() {
        if (!O?.title.trim()) {
            f(`Enter an estimate title before saving.`);
            return;
        }
        if (O.lines.some((e) => e.qty > 0 && !e.name.trim())) {
            f(
                `Name each active line item before saving, or remove the unused line.`,
            );
            return;
        }
        let t = e.estimates.find((e) => e.id === O.id),
            n = {
                ...O,
                title: O.title.trim(),
                updated: new Date().toISOString(),
            },
            r = [];
        if (t) {
            let { history: e, ...i } = t;
            if (
                JSON.stringify({ ...n, updated: i.updated }) ===
                JSON.stringify(i)
            ) {
                (f(``), u(`Estimate is already up to date.`));
                return;
            }
            r = [i, ...e];
        }
        let i = { ...n, history: r };
        (await Ee(
            {
                ...e,
                estimates: t
                    ? e.estimates.map((e) => (e.id === n.id ? i : e))
                    : [i, ...e.estimates],
            },
            `Estimate saved.`,
        )) && A(n);
    }
    async function Ae() {
        if (!j?.title.trim()) {
            f(`Enter a template name.`);
            return;
        }
        let t = e.templates.some((e) => e.id === j.id)
            ? e.templates.map((e) => (e.id === j.id ? j : e))
            : [...e.templates, j];
        (await Ee(
            { ...e, templates: t },
            `Template saved. New estimates can use these rates.`,
        )) && P(null);
    }
    let X = e.estimates
            .filter(
                (e) =>
                    (_ < 0 || e.company === _) &&
                    (!b || e.service === b) &&
                    (!C || e.status === C) &&
                    [e.title, e.address, e.client, e.number]
                        .join(` `)
                        .toLowerCase()
                        .includes(T.trim().toLowerCase()),
            )
            .sort((e, t) => t.updated.localeCompare(e.updated)),
        Z = K.map((t) => e.estimates.find((e) => e.id === t)).filter(
            (e) => !!e,
        );
    function je(e) {
        Ce((t) =>
            t.includes(e)
                ? t.filter((t) => t !== e)
                : t.length < 3
                  ? [...t, e]
                  : t,
        );
    }
    let Q = (e) => A((t) => t && { ...t, ...e }),
        $ = O && e.estimates.find((e) => e.id === O.id);
    return (0, L.jsxs)(`div`, {
        className: `shell`,
        children: [
            (0, L.jsxs)(`aside`, {
                className: `sidebar ` + (we ? `mobile-open` : ``),
                children: [
                    (0, L.jsxs)(`div`, {
                        className: `wordmark`,
                        children: [
                            (0, L.jsx)(`img`, { src: `/favicon.svg`, alt: `` }),
                            (0, L.jsxs)(`div`, {
                                children: [
                                    (0, L.jsx)(`strong`, {
                                        children: `CRYSTAL CREEK`,
                                    }),
                                    (0, L.jsx)(`span`, {
                                        children: `ESTIMATING DESK`,
                                    }),
                                ],
                            }),
                        ],
                    }),
                    (0, L.jsxs)(`div`, {
                        className: `side-group`,
                        children: [
                            (0, L.jsx)(`span`, {
                                className: `side-label`,
                                children: `WORKSPACE`,
                            }),
                            me.map(({ name: e, Icon: t }) =>
                                (0, L.jsxs)(
                                    `button`,
                                    {
                                        className:
                                            p === e && _ < 0 ? `active` : ``,
                                        onClick: () => Y(e),
                                        children: [
                                            (0, L.jsx)(t, { size: 18 }),
                                            e,
                                        ],
                                    },
                                    e,
                                ),
                            ),
                        ],
                    }),
                    (0, L.jsxs)(`div`, {
                        className: `side-group companies`,
                        children: [
                            (0, L.jsx)(`span`, {
                                className: `side-label`,
                                children: `COMPANIES`,
                            }),
                            E.map((e, t) =>
                                (0, L.jsxs)(
                                    `button`,
                                    {
                                        className: _ === t ? `active` : ``,
                                        onClick: () => Y(`Estimates`, t),
                                        children: [
                                            (0, L.jsx)(`span`, {
                                                className: `company-dot`,
                                                style: { background: e.color },
                                            }),
                                            e.name,
                                        ],
                                    },
                                    e.name,
                                ),
                            ),
                        ],
                    }),
                    (0, L.jsxs)(`div`, {
                        className: `sidebar-foot`,
                        children: [
                            (0, L.jsx)(`span`, {
                                className: `workspace-avatar`,
                                children: `CC`,
                            }),
                            (0, L.jsxs)(`div`, {
                                children: [
                                    (0, L.jsx)(`strong`, {
                                        children: `Service operations`,
                                    }),
                                    (0, L.jsx)(`span`, {
                                        children: `Greater Houston, TX`,
                                    }),
                                ],
                            }),
                        ],
                    }),
                ],
            }),
            (0, L.jsxs)(`main`, {
                className: `main`,
                children: [
                    (0, L.jsxs)(`div`, {
                        className: `topbar`,
                        children: [
                            (0, L.jsxs)(`div`, {
                                className: `crumb`,
                                children: [
                                    (0, L.jsx)(`button`, {
                                        className: `icon-button mobile-menu`,
                                        title: `Menu`,
                                        onClick: () => Te(!we),
                                        children: (0, L.jsx)(se, { size: 20 }),
                                    }),
                                    (0, L.jsx)(`span`, {
                                        children: `Workspace`,
                                    }),
                                    (0, L.jsx)(ne, { size: 13 }),
                                    (0, L.jsx)(`strong`, {
                                        children: O
                                            ? `Estimate builder`
                                            : j
                                              ? `Template editor`
                                              : p,
                                    }),
                                ],
                            }),
                            (0, L.jsxs)(`span`, {
                                className: `private`,
                                children: [
                                    (0, L.jsx)(`i`, {}),
                                    ` Private workspace`,
                                ],
                            }),
                        ],
                    }),
                    (0, L.jsxs)(`div`, {
                        className: `content`,
                        children: [
                            d &&
                                (0, L.jsxs)(`div`, {
                                    className: `notice bad`,
                                    role: `alert`,
                                    children: [
                                        d,
                                        (0, L.jsx)(`button`, {
                                            className: `icon-button`,
                                            title: `Dismiss`,
                                            onClick: () => f(``),
                                            children: (0, L.jsx)(de, {
                                                size: 15,
                                            }),
                                        }),
                                    ],
                                }),
                            c &&
                                (0, L.jsxs)(`div`, {
                                    className: `notice`,
                                    role: `status`,
                                    children: [
                                        (0, L.jsx)(v, { size: 17 }),
                                        c,
                                        (0, L.jsx)(`button`, {
                                            className: `icon-button`,
                                            title: `Dismiss`,
                                            onClick: () => u(``),
                                            children: (0, L.jsx)(de, {
                                                size: 15,
                                            }),
                                        }),
                                    ],
                                }),
                            i
                                ? O
                                    ? ye
                                        ? (0, L.jsx)(ve, {
                                              e: O,
                                              audience: be,
                                              setAudience: xe,
                                              close: () => W(!1),
                                          })
                                        : (0, L.jsxs)(L.Fragment, {
                                              children: [
                                                  (0, L.jsxs)(`button`, {
                                                      className: `back`,
                                                      onClick: () => Y(p, _),
                                                      children: [
                                                          (0, L.jsx)(h, {
                                                              size: 16,
                                                          }),
                                                          ` Back to `,
                                                          p.toLowerCase(),
                                                      ],
                                                  }),
                                                  (0, L.jsxs)(`header`, {
                                                      className: `page-heading`,
                                                      children: [
                                                          (0, L.jsxs)(`div`, {
                                                              children: [
                                                                  (0, L.jsx)(
                                                                      `span`,
                                                                      {
                                                                          className: `eyebrow`,
                                                                          children:
                                                                              O.number,
                                                                      },
                                                                  ),
                                                                  (0, L.jsx)(
                                                                      `h1`,
                                                                      {
                                                                          children:
                                                                              $
                                                                                  ? O.title ||
                                                                                    `Estimate builder`
                                                                                  : `New estimate`,
                                                                      },
                                                                  ),
                                                                  (0, L.jsx)(
                                                                      `p`,
                                                                      {
                                                                          children: `Build the scope, check pricing, and prepare a proposal.`,
                                                                      },
                                                                  ),
                                                              ],
                                                          }),
                                                          (0, L.jsxs)(`div`, {
                                                              className: `actions`,
                                                              children: [
                                                                  $ &&
                                                                      (0,
                                                                      L.jsxs)(
                                                                          `button`,
                                                                          {
                                                                              className: `button secondary`,
                                                                              onClick:
                                                                                  () =>
                                                                                      G(
                                                                                          !Se,
                                                                                      ),
                                                                              children:
                                                                                  [
                                                                                      (0,
                                                                                      L.jsx)(
                                                                                          re,
                                                                                          {
                                                                                              size: 16,
                                                                                          },
                                                                                      ),
                                                                                      ` History`,
                                                                                  ],
                                                                          },
                                                                      ),
                                                                  (0, L.jsxs)(
                                                                      `button`,
                                                                      {
                                                                          className: `button secondary`,
                                                                          onClick:
                                                                              () => {
                                                                                  (W(
                                                                                      !0,
                                                                                  ),
                                                                                      window.scrollTo(
                                                                                          0,
                                                                                          0,
                                                                                      ));
                                                                              },
                                                                          children:
                                                                              [
                                                                                  (0,
                                                                                  L.jsx)(
                                                                                      ie,
                                                                                      {
                                                                                          size: 16,
                                                                                      },
                                                                                  ),
                                                                                  ` Proposal`,
                                                                              ],
                                                                      },
                                                                  ),
                                                                  (0, L.jsxs)(
                                                                      `button`,
                                                                      {
                                                                          className: `button primary`,
                                                                          disabled:
                                                                              o,
                                                                          onClick:
                                                                              ke,
                                                                          children:
                                                                              [
                                                                                  (0,
                                                                                  L.jsx)(
                                                                                      v,
                                                                                      {
                                                                                          size: 17,
                                                                                      },
                                                                                  ),
                                                                                  ` `,
                                                                                  o
                                                                                      ? `Saving…`
                                                                                      : `Save estimate`,
                                                                              ],
                                                                      },
                                                                  ),
                                                              ],
                                                          }),
                                                      ],
                                                  }),
                                                  Se &&
                                                      $ &&
                                                      (0, L.jsxs)(`section`, {
                                                          className: `panel section`,
                                                          children: [
                                                              (0, L.jsx)(`h3`, {
                                                                  children: `Revision history`,
                                                              }),
                                                              (0, L.jsx)(`p`, {
                                                                  className: `muted`,
                                                                  children: `Restoring loads a prior snapshot into the editor. Save to create a new revision.`,
                                                              }),
                                                              (0, L.jsxs)(
                                                                  `div`,
                                                                  {
                                                                      className: `history-row`,
                                                                      children:
                                                                          [
                                                                              (0,
                                                                              L.jsxs)(
                                                                                  `strong`,
                                                                                  {
                                                                                      children:
                                                                                          [
                                                                                              `Revision `,
                                                                                              $
                                                                                                  .history
                                                                                                  .length +
                                                                                                  1,
                                                                                              ` · Current saved`,
                                                                                          ],
                                                                                  },
                                                                              ),
                                                                              (0,
                                                                              L.jsx)(
                                                                                  `span`,
                                                                                  {
                                                                                      children:
                                                                                          I(
                                                                                              $.updated,
                                                                                          ),
                                                                                  },
                                                                              ),
                                                                              (0,
                                                                              L.jsx)(
                                                                                  H,
                                                                                  {
                                                                                      status: $.status,
                                                                                  },
                                                                              ),
                                                                              (0,
                                                                              L.jsx)(
                                                                                  `strong`,
                                                                                  {
                                                                                      children:
                                                                                          M(
                                                                                              F(
                                                                                                  $,
                                                                                              )
                                                                                                  .total,
                                                                                          ),
                                                                                  },
                                                                              ),
                                                                          ],
                                                                  },
                                                              ),
                                                              $.history.map(
                                                                  (e, t) =>
                                                                      (0,
                                                                      L.jsxs)(
                                                                          `div`,
                                                                          {
                                                                              className: `history-row`,
                                                                              children:
                                                                                  [
                                                                                      (0,
                                                                                      L.jsxs)(
                                                                                          `strong`,
                                                                                          {
                                                                                              children:
                                                                                                  [
                                                                                                      `Revision `,
                                                                                                      $
                                                                                                          .history
                                                                                                          .length -
                                                                                                          t,
                                                                                                  ],
                                                                                          },
                                                                                      ),
                                                                                      (0,
                                                                                      L.jsx)(
                                                                                          `span`,
                                                                                          {
                                                                                              children:
                                                                                                  I(
                                                                                                      e.updated,
                                                                                                  ),
                                                                                          },
                                                                                      ),
                                                                                      (0,
                                                                                      L.jsx)(
                                                                                          H,
                                                                                          {
                                                                                              status: e.status,
                                                                                          },
                                                                                      ),
                                                                                      (0,
                                                                                      L.jsx)(
                                                                                          `strong`,
                                                                                          {
                                                                                              children:
                                                                                                  M(
                                                                                                      F(
                                                                                                          e,
                                                                                                      )
                                                                                                          .total,
                                                                                                  ),
                                                                                          },
                                                                                      ),
                                                                                      (0,
                                                                                      L.jsx)(
                                                                                          `button`,
                                                                                          {
                                                                                              className: `button secondary compact`,
                                                                                              onClick:
                                                                                                  () =>
                                                                                                      A(
                                                                                                          structuredClone(
                                                                                                              e,
                                                                                                          ),
                                                                                                      ),
                                                                                              children: `Load revision`,
                                                                                          },
                                                                                      ),
                                                                                  ],
                                                                          },
                                                                          t,
                                                                      ),
                                                              ),
                                                          ],
                                                      }),
                                                  (0, L.jsxs)(`div`, {
                                                      className: `editor-grid`,
                                                      children: [
                                                          (0, L.jsxs)(`div`, {
                                                              className: `stack`,
                                                              children: [
                                                                  (0, L.jsxs)(
                                                                      `section`,
                                                                      {
                                                                          className: `panel section`,
                                                                          children:
                                                                              [
                                                                                  (0,
                                                                                  L.jsx)(
                                                                                      `h3`,
                                                                                      {
                                                                                          children: `Estimate details`,
                                                                                      },
                                                                                  ),
                                                                                  (0,
                                                                                  L.jsxs)(
                                                                                      `div`,
                                                                                      {
                                                                                          className: `fields`,
                                                                                          children:
                                                                                              [
                                                                                                  (0,
                                                                                                  L.jsxs)(
                                                                                                      `label`,
                                                                                                      {
                                                                                                          children:
                                                                                                              [
                                                                                                                  `Estimate title`,
                                                                                                                  (0,
                                                                                                                  L.jsx)(
                                                                                                                      `input`,
                                                                                                                      {
                                                                                                                          value: O.title,
                                                                                                                          placeholder: `e.g. Evaporator coil replacement`,
                                                                                                                          onChange:
                                                                                                                              (
                                                                                                                                  e,
                                                                                                                              ) =>
                                                                                                                                  Q(
                                                                                                                                      {
                                                                                                                                          title: e
                                                                                                                                              .target
                                                                                                                                              .value,
                                                                                                                                      },
                                                                                                                                  ),
                                                                                                                      },
                                                                                                                  ),
                                                                                                              ],
                                                                                                      },
                                                                                                  ),
                                                                                                  (0,
                                                                                                  L.jsxs)(
                                                                                                      `label`,
                                                                                                      {
                                                                                                          children:
                                                                                                              [
                                                                                                                  `Approval status`,
                                                                                                                  (0,
                                                                                                                  L.jsx)(
                                                                                                                      `select`,
                                                                                                                      {
                                                                                                                          value: O.status,
                                                                                                                          onChange:
                                                                                                                              (
                                                                                                                                  e,
                                                                                                                              ) =>
                                                                                                                                  Q(
                                                                                                                                      {
                                                                                                                                          status: e
                                                                                                                                              .target
                                                                                                                                              .value,
                                                                                                                                      },
                                                                                                                                  ),
                                                                                                                          children:
                                                                                                                              fe.map(
                                                                                                                                  (
                                                                                                                                      e,
                                                                                                                                  ) =>
                                                                                                                                      (0,
                                                                                                                                      L.jsx)(
                                                                                                                                          `option`,
                                                                                                                                          {
                                                                                                                                              children:
                                                                                                                                                  e,
                                                                                                                                          },
                                                                                                                                          e,
                                                                                                                                      ),
                                                                                                                              ),
                                                                                                                      },
                                                                                                                  ),
                                                                                                              ],
                                                                                                      },
                                                                                                  ),
                                                                                                  (0,
                                                                                                  L.jsxs)(
                                                                                                      `label`,
                                                                                                      {
                                                                                                          children:
                                                                                                              [
                                                                                                                  `Company`,
                                                                                                                  (0,
                                                                                                                  L.jsx)(
                                                                                                                      `select`,
                                                                                                                      {
                                                                                                                          value: O.company,
                                                                                                                          onChange:
                                                                                                                              (
                                                                                                                                  e,
                                                                                                                              ) =>
                                                                                                                                  Q(
                                                                                                                                      {
                                                                                                                                          company:
                                                                                                                                              Number(
                                                                                                                                                  e
                                                                                                                                                      .target
                                                                                                                                                      .value,
                                                                                                                                              ),
                                                                                                                                      },
                                                                                                                                  ),
                                                                                                                          children:
                                                                                                                              E.map(
                                                                                                                                  (
                                                                                                                                      e,
                                                                                                                                      t,
                                                                                                                                  ) =>
                                                                                                                                      (0,
                                                                                                                                      L.jsx)(
                                                                                                                                          `option`,
                                                                                                                                          {
                                                                                                                                              value: t,
                                                                                                                                              children:
                                                                                                                                                  e.name,
                                                                                                                                          },
                                                                                                                                          e.name,
                                                                                                                                      ),
                                                                                                                              ),
                                                                                                                      },
                                                                                                                  ),
                                                                                                              ],
                                                                                                      },
                                                                                                  ),
                                                                                                  (0,
                                                                                                  L.jsxs)(
                                                                                                      `label`,
                                                                                                      {
                                                                                                          children:
                                                                                                              [
                                                                                                                  `Service type`,
                                                                                                                  (0,
                                                                                                                  L.jsx)(
                                                                                                                      `select`,
                                                                                                                      {
                                                                                                                          value: O.service,
                                                                                                                          onChange:
                                                                                                                              (
                                                                                                                                  e,
                                                                                                                              ) =>
                                                                                                                                  Q(
                                                                                                                                      {
                                                                                                                                          service:
                                                                                                                                              e
                                                                                                                                                  .target
                                                                                                                                                  .value,
                                                                                                                                      },
                                                                                                                                  ),
                                                                                                                          children:
                                                                                                                              D.map(
                                                                                                                                  (
                                                                                                                                      e,
                                                                                                                                  ) =>
                                                                                                                                      (0,
                                                                                                                                      L.jsx)(
                                                                                                                                          `option`,
                                                                                                                                          {
                                                                                                                                              children:
                                                                                                                                                  e,
                                                                                                                                          },
                                                                                                                                          e,
                                                                                                                                      ),
                                                                                                                              ),
                                                                                                                      },
                                                                                                                  ),
                                                                                                              ],
                                                                                                      },
                                                                                                  ),
                                                                                                  (0,
                                                                                                  L.jsxs)(
                                                                                                      `label`,
                                                                                                      {
                                                                                                          children:
                                                                                                              [
                                                                                                                  `Customer or owner`,
                                                                                                                  (0,
                                                                                                                  L.jsx)(
                                                                                                                      `input`,
                                                                                                                      {
                                                                                                                          value: O.client,
                                                                                                                          onChange:
                                                                                                                              (
                                                                                                                                  e,
                                                                                                                              ) =>
                                                                                                                                  Q(
                                                                                                                                      {
                                                                                                                                          client: e
                                                                                                                                              .target
                                                                                                                                              .value,
                                                                                                                                      },
                                                                                                                                  ),
                                                                                                                      },
                                                                                                                  ),
                                                                                                              ],
                                                                                                      },
                                                                                                  ),
                                                                                                  (0,
                                                                                                  L.jsxs)(
                                                                                                      `label`,
                                                                                                      {
                                                                                                          children:
                                                                                                              [
                                                                                                                  `Service address`,
                                                                                                                  (0,
                                                                                                                  L.jsx)(
                                                                                                                      `input`,
                                                                                                                      {
                                                                                                                          value: O.address,
                                                                                                                          placeholder: `Street, city, ZIP`,
                                                                                                                          onChange:
                                                                                                                              (
                                                                                                                                  e,
                                                                                                                              ) =>
                                                                                                                                  Q(
                                                                                                                                      {
                                                                                                                                          address:
                                                                                                                                              e
                                                                                                                                                  .target
                                                                                                                                                  .value,
                                                                                                                                      },
                                                                                                                                  ),
                                                                                                                      },
                                                                                                                  ),
                                                                                                              ],
                                                                                                      },
                                                                                                  ),
                                                                                              ],
                                                                                      },
                                                                                  ),
                                                                              ],
                                                                      },
                                                                  ),
                                                                  (0, L.jsxs)(
                                                                      `section`,
                                                                      {
                                                                          className: `panel section`,
                                                                          children:
                                                                              [
                                                                                  (0,
                                                                                  L.jsx)(
                                                                                      `h3`,
                                                                                      {
                                                                                          children: `Line items`,
                                                                                      },
                                                                                  ),
                                                                                  (0,
                                                                                  L.jsx)(
                                                                                      `p`,
                                                                                      {
                                                                                          className: `muted`,
                                                                                          children: `Markup applies to unit costs. A fixed sell price overrides markup for that line.`,
                                                                                      },
                                                                                  ),
                                                                                  (0,
                                                                                  L.jsx)(
                                                                                      ge,
                                                                                      {
                                                                                          value: O.lines,
                                                                                          change: (
                                                                                              e,
                                                                                          ) =>
                                                                                              Q(
                                                                                                  {
                                                                                                      lines: e,
                                                                                                  },
                                                                                              ),
                                                                                          markup: O
                                                                                              .pricing
                                                                                              .markup,
                                                                                      },
                                                                                  ),
                                                                              ],
                                                                      },
                                                                  ),
                                                                  (0, L.jsxs)(
                                                                      `section`,
                                                                      {
                                                                          className: `panel section`,
                                                                          children:
                                                                              [
                                                                                  (0,
                                                                                  L.jsx)(
                                                                                      `h3`,
                                                                                      {
                                                                                          children: `Pricing adjustments`,
                                                                                      },
                                                                                  ),
                                                                                  (0,
                                                                                  L.jsx)(
                                                                                      he,
                                                                                      {
                                                                                          value: O.pricing,
                                                                                          change: (
                                                                                              e,
                                                                                          ) =>
                                                                                              Q(
                                                                                                  {
                                                                                                      pricing:
                                                                                                          e,
                                                                                                  },
                                                                                              ),
                                                                                      },
                                                                                  ),
                                                                              ],
                                                                      },
                                                                  ),
                                                                  (0, L.jsxs)(
                                                                      `section`,
                                                                      {
                                                                          className: `panel section`,
                                                                          children:
                                                                              [
                                                                                  (0,
                                                                                  L.jsx)(
                                                                                      `h3`,
                                                                                      {
                                                                                          children: `Scope, terms and notes`,
                                                                                      },
                                                                                  ),
                                                                                  (0,
                                                                                  L.jsxs)(
                                                                                      `div`,
                                                                                      {
                                                                                          className: `fields one`,
                                                                                          children:
                                                                                              [
                                                                                                  (0,
                                                                                                  L.jsxs)(
                                                                                                      `label`,
                                                                                                      {
                                                                                                          children:
                                                                                                              [
                                                                                                                  `Proposal scope`,
                                                                                                                  (0,
                                                                                                                  L.jsx)(
                                                                                                                      `textarea`,
                                                                                                                      {
                                                                                                                          value: O.scope,
                                                                                                                          onChange:
                                                                                                                              (
                                                                                                                                  e,
                                                                                                                              ) =>
                                                                                                                                  Q(
                                                                                                                                      {
                                                                                                                                          scope: e
                                                                                                                                              .target
                                                                                                                                              .value,
                                                                                                                                      },
                                                                                                                                  ),
                                                                                                                      },
                                                                                                                  ),
                                                                                                              ],
                                                                                                      },
                                                                                                  ),
                                                                                                  (0,
                                                                                                  L.jsxs)(
                                                                                                      `label`,
                                                                                                      {
                                                                                                          children:
                                                                                                              [
                                                                                                                  `Proposal terms and exclusions`,
                                                                                                                  (0,
                                                                                                                  L.jsx)(
                                                                                                                      `textarea`,
                                                                                                                      {
                                                                                                                          value: O.terms,
                                                                                                                          onChange:
                                                                                                                              (
                                                                                                                                  e,
                                                                                                                              ) =>
                                                                                                                                  Q(
                                                                                                                                      {
                                                                                                                                          terms: e
                                                                                                                                              .target
                                                                                                                                              .value,
                                                                                                                                      },
                                                                                                                                  ),
                                                                                                                      },
                                                                                                                  ),
                                                                                                              ],
                                                                                                      },
                                                                                                  ),
                                                                                                  (0,
                                                                                                  L.jsxs)(
                                                                                                      `label`,
                                                                                                      {
                                                                                                          children:
                                                                                                              [
                                                                                                                  `Internal notes`,
                                                                                                                  (0,
                                                                                                                  L.jsx)(
                                                                                                                      `textarea`,
                                                                                                                      {
                                                                                                                          placeholder: `Approval details, job conditions, vendor quotes…`,
                                                                                                                          value: O.notes,
                                                                                                                          onChange:
                                                                                                                              (
                                                                                                                                  e,
                                                                                                                              ) =>
                                                                                                                                  Q(
                                                                                                                                      {
                                                                                                                                          notes: e
                                                                                                                                              .target
                                                                                                                                              .value,
                                                                                                                                      },
                                                                                                                                  ),
                                                                                                                      },
                                                                                                                  ),
                                                                                                              ],
                                                                                                      },
                                                                                                  ),
                                                                                              ],
                                                                                      },
                                                                                  ),
                                                                              ],
                                                                      },
                                                                  ),
                                                              ],
                                                          }),
                                                          (0, L.jsx)(_e, {
                                                              e: O,
                                                          }),
                                                      ],
                                                  }),
                                              ],
                                          })
                                    : j
                                      ? (0, L.jsxs)(L.Fragment, {
                                            children: [
                                                (0, L.jsxs)(`button`, {
                                                    className: `back`,
                                                    onClick: () => P(null),
                                                    children: [
                                                        (0, L.jsx)(h, {
                                                            size: 16,
                                                        }),
                                                        ` Back to templates`,
                                                    ],
                                                }),
                                                (0, L.jsxs)(`header`, {
                                                    className: `page-heading`,
                                                    children: [
                                                        (0, L.jsxs)(`div`, {
                                                            children: [
                                                                (0, L.jsx)(
                                                                    `span`,
                                                                    {
                                                                        className: `eyebrow`,
                                                                        children: `SERVICE CATALOG`,
                                                                    },
                                                                ),
                                                                (0, L.jsx)(
                                                                    `h1`,
                                                                    {
                                                                        children: `Edit service template`,
                                                                    },
                                                                ),
                                                                (0, L.jsx)(
                                                                    `p`,
                                                                    {
                                                                        children: `Update reusable scope and unit rates for future estimates.`,
                                                                    },
                                                                ),
                                                            ],
                                                        }),
                                                        (0, L.jsxs)(`button`, {
                                                            className: `button primary`,
                                                            disabled: o,
                                                            onClick: Ae,
                                                            children: [
                                                                (0, L.jsx)(v, {
                                                                    size: 16,
                                                                }),
                                                                ` Save template`,
                                                            ],
                                                        }),
                                                    ],
                                                }),
                                                (0, L.jsxs)(`div`, {
                                                    className: `stack`,
                                                    children: [
                                                        (0, L.jsxs)(`section`, {
                                                            className: `panel section`,
                                                            children: [
                                                                (0, L.jsx)(
                                                                    `h3`,
                                                                    {
                                                                        children: `Template details`,
                                                                    },
                                                                ),
                                                                (0, L.jsxs)(
                                                                    `div`,
                                                                    {
                                                                        className: `fields`,
                                                                        children:
                                                                            [
                                                                                (0,
                                                                                L.jsxs)(
                                                                                    `label`,
                                                                                    {
                                                                                        children:
                                                                                            [
                                                                                                `Template name`,
                                                                                                (0,
                                                                                                L.jsx)(
                                                                                                    `input`,
                                                                                                    {
                                                                                                        value: j.title,
                                                                                                        onChange:
                                                                                                            (
                                                                                                                e,
                                                                                                            ) =>
                                                                                                                P(
                                                                                                                    {
                                                                                                                        ...j,
                                                                                                                        title: e
                                                                                                                            .target
                                                                                                                            .value,
                                                                                                                    },
                                                                                                                ),
                                                                                                    },
                                                                                                ),
                                                                                            ],
                                                                                    },
                                                                                ),
                                                                                (0,
                                                                                L.jsxs)(
                                                                                    `label`,
                                                                                    {
                                                                                        children:
                                                                                            [
                                                                                                `Company`,
                                                                                                (0,
                                                                                                L.jsx)(
                                                                                                    `select`,
                                                                                                    {
                                                                                                        value: j.company,
                                                                                                        onChange:
                                                                                                            (
                                                                                                                e,
                                                                                                            ) =>
                                                                                                                P(
                                                                                                                    {
                                                                                                                        ...j,
                                                                                                                        company:
                                                                                                                            Number(
                                                                                                                                e
                                                                                                                                    .target
                                                                                                                                    .value,
                                                                                                                            ),
                                                                                                                    },
                                                                                                                ),
                                                                                                        children:
                                                                                                            E.map(
                                                                                                                (
                                                                                                                    e,
                                                                                                                    t,
                                                                                                                ) =>
                                                                                                                    (0,
                                                                                                                    L.jsx)(
                                                                                                                        `option`,
                                                                                                                        {
                                                                                                                            value: t,
                                                                                                                            children:
                                                                                                                                e.name,
                                                                                                                        },
                                                                                                                        e.name,
                                                                                                                    ),
                                                                                                            ),
                                                                                                    },
                                                                                                ),
                                                                                            ],
                                                                                    },
                                                                                ),
                                                                                (0,
                                                                                L.jsxs)(
                                                                                    `label`,
                                                                                    {
                                                                                        children:
                                                                                            [
                                                                                                `Service type`,
                                                                                                (0,
                                                                                                L.jsx)(
                                                                                                    `select`,
                                                                                                    {
                                                                                                        value: j.service,
                                                                                                        onChange:
                                                                                                            (
                                                                                                                e,
                                                                                                            ) =>
                                                                                                                P(
                                                                                                                    {
                                                                                                                        ...j,
                                                                                                                        service:
                                                                                                                            e
                                                                                                                                .target
                                                                                                                                .value,
                                                                                                                    },
                                                                                                                ),
                                                                                                        children:
                                                                                                            D.map(
                                                                                                                (
                                                                                                                    e,
                                                                                                                ) =>
                                                                                                                    (0,
                                                                                                                    L.jsx)(
                                                                                                                        `option`,
                                                                                                                        {
                                                                                                                            children:
                                                                                                                                e,
                                                                                                                        },
                                                                                                                        e,
                                                                                                                    ),
                                                                                                            ),
                                                                                                    },
                                                                                                ),
                                                                                            ],
                                                                                    },
                                                                                ),
                                                                                (0,
                                                                                L.jsxs)(
                                                                                    `label`,
                                                                                    {
                                                                                        className: `wide`,
                                                                                        children:
                                                                                            [
                                                                                                `Scope`,
                                                                                                (0,
                                                                                                L.jsx)(
                                                                                                    `textarea`,
                                                                                                    {
                                                                                                        value: j.scope,
                                                                                                        onChange:
                                                                                                            (
                                                                                                                e,
                                                                                                            ) =>
                                                                                                                P(
                                                                                                                    {
                                                                                                                        ...j,
                                                                                                                        scope: e
                                                                                                                            .target
                                                                                                                            .value,
                                                                                                                    },
                                                                                                                ),
                                                                                                    },
                                                                                                ),
                                                                                            ],
                                                                                    },
                                                                                ),
                                                                                (0,
                                                                                L.jsxs)(
                                                                                    `label`,
                                                                                    {
                                                                                        className: `wide`,
                                                                                        children:
                                                                                            [
                                                                                                `Terms`,
                                                                                                (0,
                                                                                                L.jsx)(
                                                                                                    `textarea`,
                                                                                                    {
                                                                                                        value: j.terms,
                                                                                                        onChange:
                                                                                                            (
                                                                                                                e,
                                                                                                            ) =>
                                                                                                                P(
                                                                                                                    {
                                                                                                                        ...j,
                                                                                                                        terms: e
                                                                                                                            .target
                                                                                                                            .value,
                                                                                                                    },
                                                                                                                ),
                                                                                                    },
                                                                                                ),
                                                                                            ],
                                                                                    },
                                                                                ),
                                                                            ],
                                                                    },
                                                                ),
                                                            ],
                                                        }),
                                                        (0, L.jsxs)(`section`, {
                                                            className: `panel section`,
                                                            children: [
                                                                (0, L.jsx)(
                                                                    `h3`,
                                                                    {
                                                                        children: `Reusable line items`,
                                                                    },
                                                                ),
                                                                (0, L.jsx)(ge, {
                                                                    value: j.lines,
                                                                    change: (
                                                                        e,
                                                                    ) =>
                                                                        P({
                                                                            ...j,
                                                                            lines: e,
                                                                        }),
                                                                }),
                                                            ],
                                                        }),
                                                    ],
                                                }),
                                            ],
                                        })
                                      : p === `Estimates`
                                        ? (0, L.jsxs)(L.Fragment, {
                                              children: [
                                                  (0, L.jsxs)(`header`, {
                                                      className: `page-heading dashboard-heading`,
                                                      children: [
                                                          (0, L.jsxs)(`div`, {
                                                              children: [
                                                                  (0, L.jsxs)(
                                                                      `span`,
                                                                      {
                                                                          className: `eyebrow`,
                                                                          children:
                                                                              [
                                                                                  (0,
                                                                                  L.jsx)(
                                                                                      `i`,
                                                                                      {},
                                                                                  ),
                                                                                  ` GREATER HOUSTON OPERATIONS`,
                                                                              ],
                                                                      },
                                                                  ),
                                                                  (0, L.jsx)(
                                                                      `h1`,
                                                                      {
                                                                          children:
                                                                              _ <
                                                                              0
                                                                                  ? `Estimating desk`
                                                                                  : E[
                                                                                        _
                                                                                    ]
                                                                                        .name,
                                                                      },
                                                                  ),
                                                                  (0, L.jsx)(
                                                                      `p`,
                                                                      {
                                                                          children: `Build service pricing, track approvals, and turn estimates into proposals.`,
                                                                      },
                                                                  ),
                                                              ],
                                                          }),
                                                          (0, L.jsxs)(
                                                              `button`,
                                                              {
                                                                  className: `button primary`,
                                                                  onClick: () =>
                                                                      De(),
                                                                  children: [
                                                                      (0,
                                                                      L.jsx)(
                                                                          x,
                                                                          {
                                                                              size: 19,
                                                                          },
                                                                      ),
                                                                      ` New estimate`,
                                                                  ],
                                                              },
                                                          ),
                                                      ],
                                                  }),
                                                  (0, L.jsx)(`div`, {
                                                      className: `metrics`,
                                                      children: [
                                                          {
                                                              label: `Total estimates`,
                                                              value: X.length,
                                                              sub: `Estimates in this view`,
                                                              Icon: ie,
                                                              className: `green`,
                                                          },
                                                          {
                                                              label: `Pending approval`,
                                                              value: X.filter(
                                                                  (e) =>
                                                                      e.status ===
                                                                      `Pending approval`,
                                                              ).length,
                                                              sub: `Awaiting a decision`,
                                                              Icon: re,
                                                              className: `orange`,
                                                          },
                                                          {
                                                              label: `Approved value`,
                                                              value: M(
                                                                  X.filter(
                                                                      (e) =>
                                                                          e.status ===
                                                                          `Approved`,
                                                                  ).reduce(
                                                                      (e, t) =>
                                                                          e +
                                                                          F(t)
                                                                              .total,
                                                                      0,
                                                                  ),
                                                              ),
                                                              sub: `Total approved estimates`,
                                                              Icon: v,
                                                              className: `blue`,
                                                          },
                                                          {
                                                              label: `Service templates`,
                                                              value: e.templates.filter(
                                                                  (e) =>
                                                                      (_ < 0 ||
                                                                          e.company ===
                                                                              _) &&
                                                                      (!b ||
                                                                          e.service ===
                                                                              b),
                                                              ).length,
                                                              sub: `Reusable scopes and rates`,
                                                              Icon: oe,
                                                              className: `purple`,
                                                          },
                                                      ].map(
                                                          ({
                                                              label: e,
                                                              value: t,
                                                              sub: n,
                                                              Icon: r,
                                                              className: i,
                                                          }) =>
                                                              (0, L.jsxs)(
                                                                  `div`,
                                                                  {
                                                                      className: `panel metric`,
                                                                      children:
                                                                          [
                                                                              (0,
                                                                              L.jsxs)(
                                                                                  `div`,
                                                                                  {
                                                                                      children:
                                                                                          [
                                                                                              (0,
                                                                                              L.jsx)(
                                                                                                  `span`,
                                                                                                  {
                                                                                                      children:
                                                                                                          e,
                                                                                                  },
                                                                                              ),
                                                                                              (0,
                                                                                              L.jsx)(
                                                                                                  `span`,
                                                                                                  {
                                                                                                      className:
                                                                                                          `metric-icon ` +
                                                                                                          i,
                                                                                                      children:
                                                                                                          (0,
                                                                                                          L.jsx)(
                                                                                                              r,
                                                                                                              {
                                                                                                                  size: 17,
                                                                                                              },
                                                                                                          ),
                                                                                                  },
                                                                                              ),
                                                                                          ],
                                                                                  },
                                                                              ),
                                                                              (0,
                                                                              L.jsx)(
                                                                                  `strong`,
                                                                                  {
                                                                                      children:
                                                                                          t,
                                                                                  },
                                                                              ),
                                                                              (0,
                                                                              L.jsx)(
                                                                                  `small`,
                                                                                  {
                                                                                      children:
                                                                                          n,
                                                                                  },
                                                                              ),
                                                                          ],
                                                                  },
                                                                  e,
                                                              ),
                                                      ),
                                                  }),
                                                  (0, L.jsxs)(`section`, {
                                                      className: `panel estimates-panel`,
                                                      children: [
                                                          (0, L.jsxs)(`div`, {
                                                              className: `table-heading`,
                                                              children: [
                                                                  (0, L.jsxs)(
                                                                      `div`,
                                                                      {
                                                                          children:
                                                                              [
                                                                                  (0,
                                                                                  L.jsx)(
                                                                                      `h2`,
                                                                                      {
                                                                                          children: `Estimates`,
                                                                                      },
                                                                                  ),
                                                                                  (0,
                                                                                  L.jsx)(
                                                                                      `p`,
                                                                                      {
                                                                                          children: `Manage service quotes across your companies.`,
                                                                                      },
                                                                                  ),
                                                                              ],
                                                                      },
                                                                  ),
                                                                  (0, L.jsxs)(
                                                                      `button`,
                                                                      {
                                                                          className: `button secondary compact`,
                                                                          onClick:
                                                                              () =>
                                                                                  Y(
                                                                                      `Compare estimates`,
                                                                                  ),
                                                                          children:
                                                                              [
                                                                                  (0,
                                                                                  L.jsx)(
                                                                                      ae,
                                                                                      {
                                                                                          size: 16,
                                                                                      },
                                                                                  ),
                                                                                  ` Compare`,
                                                                                  K.length
                                                                                      ? ` (` +
                                                                                        K.length +
                                                                                        `)`
                                                                                      : ``,
                                                                              ],
                                                                      },
                                                                  ),
                                                              ],
                                                          }),
                                                          (0, L.jsxs)(`div`, {
                                                              className: `filters`,
                                                              children: [
                                                                  (0, L.jsxs)(
                                                                      `label`,
                                                                      {
                                                                          className: `search`,
                                                                          children:
                                                                              [
                                                                                  (0,
                                                                                  L.jsx)(
                                                                                      ce,
                                                                                      {
                                                                                          size: 17,
                                                                                      },
                                                                                  ),
                                                                                  (0,
                                                                                  L.jsx)(
                                                                                      `input`,
                                                                                      {
                                                                                          'aria-label': `Search estimates`,
                                                                                          placeholder: `Search estimates…`,
                                                                                          value: T,
                                                                                          onChange:
                                                                                              (
                                                                                                  e,
                                                                                              ) =>
                                                                                                  ue(
                                                                                                      e
                                                                                                          .target
                                                                                                          .value,
                                                                                                  ),
                                                                                      },
                                                                                  ),
                                                                              ],
                                                                      },
                                                                  ),
                                                                  (0, L.jsxs)(
                                                                      `div`,
                                                                      {
                                                                          className: `selects`,
                                                                          children:
                                                                              [
                                                                                  (0,
                                                                                  L.jsxs)(
                                                                                      `select`,
                                                                                      {
                                                                                          'aria-label': `Company filter`,
                                                                                          value: _,
                                                                                          onChange:
                                                                                              (
                                                                                                  e,
                                                                                              ) =>
                                                                                                  y(
                                                                                                      Number(
                                                                                                          e
                                                                                                              .target
                                                                                                              .value,
                                                                                                      ),
                                                                                                  ),
                                                                                          children:
                                                                                              [
                                                                                                  (0,
                                                                                                  L.jsx)(
                                                                                                      `option`,
                                                                                                      {
                                                                                                          value: -1,
                                                                                                          children: `All companies`,
                                                                                                      },
                                                                                                  ),
                                                                                                  E.map(
                                                                                                      (
                                                                                                          e,
                                                                                                          t,
                                                                                                      ) =>
                                                                                                          (0,
                                                                                                          L.jsx)(
                                                                                                              `option`,
                                                                                                              {
                                                                                                                  value: t,
                                                                                                                  children:
                                                                                                                      e.name,
                                                                                                              },
                                                                                                              e.name,
                                                                                                          ),
                                                                                                  ),
                                                                                              ],
                                                                                      },
                                                                                  ),
                                                                                  (0,
                                                                                  L.jsxs)(
                                                                                      `select`,
                                                                                      {
                                                                                          'aria-label': `Service filter`,
                                                                                          value: b,
                                                                                          onChange:
                                                                                              (
                                                                                                  e,
                                                                                              ) =>
                                                                                                  S(
                                                                                                      e
                                                                                                          .target
                                                                                                          .value,
                                                                                                  ),
                                                                                          children:
                                                                                              [
                                                                                                  (0,
                                                                                                  L.jsx)(
                                                                                                      `option`,
                                                                                                      {
                                                                                                          value: ``,
                                                                                                          children: `All services`,
                                                                                                      },
                                                                                                  ),
                                                                                                  D.map(
                                                                                                      (
                                                                                                          e,
                                                                                                      ) =>
                                                                                                          (0,
                                                                                                          L.jsx)(
                                                                                                              `option`,
                                                                                                              {
                                                                                                                  children:
                                                                                                                      e,
                                                                                                              },
                                                                                                              e,
                                                                                                          ),
                                                                                                  ),
                                                                                              ],
                                                                                      },
                                                                                  ),
                                                                                  (0,
                                                                                  L.jsxs)(
                                                                                      `select`,
                                                                                      {
                                                                                          'aria-label': `Status filter`,
                                                                                          value: C,
                                                                                          onChange:
                                                                                              (
                                                                                                  e,
                                                                                              ) =>
                                                                                                  w(
                                                                                                      e
                                                                                                          .target
                                                                                                          .value,
                                                                                                  ),
                                                                                          children:
                                                                                              [
                                                                                                  (0,
                                                                                                  L.jsx)(
                                                                                                      `option`,
                                                                                                      {
                                                                                                          value: ``,
                                                                                                          children: `All statuses`,
                                                                                                      },
                                                                                                  ),
                                                                                                  fe.map(
                                                                                                      (
                                                                                                          e,
                                                                                                      ) =>
                                                                                                          (0,
                                                                                                          L.jsx)(
                                                                                                              `option`,
                                                                                                              {
                                                                                                                  children:
                                                                                                                      e,
                                                                                                              },
                                                                                                              e,
                                                                                                          ),
                                                                                                  ),
                                                                                              ],
                                                                                      },
                                                                                  ),
                                                                              ],
                                                                      },
                                                                  ),
                                                              ],
                                                          }),
                                                          (0, L.jsx)(`div`, {
                                                              className: `scroll`,
                                                              children: (0,
                                                              L.jsxs)(`table`, {
                                                                  className: `estimate-table`,
                                                                  children: [
                                                                      (0,
                                                                      L.jsx)(
                                                                          `thead`,
                                                                          {
                                                                              children:
                                                                                  (0,
                                                                                  L.jsxs)(
                                                                                      `tr`,
                                                                                      {
                                                                                          children:
                                                                                              [
                                                                                                  (0,
                                                                                                  L.jsx)(
                                                                                                      `th`,
                                                                                                      {
                                                                                                          className: `check-col`,
                                                                                                          children:
                                                                                                              (0,
                                                                                                              L.jsx)(
                                                                                                                  le,
                                                                                                                  {
                                                                                                                      size: 14,
                                                                                                                  },
                                                                                                              ),
                                                                                                      },
                                                                                                  ),
                                                                                                  (0,
                                                                                                  L.jsx)(
                                                                                                      `th`,
                                                                                                      {
                                                                                                          children: `Estimate`,
                                                                                                      },
                                                                                                  ),
                                                                                                  (0,
                                                                                                  L.jsx)(
                                                                                                      `th`,
                                                                                                      {
                                                                                                          children: `Company`,
                                                                                                      },
                                                                                                  ),
                                                                                                  (0,
                                                                                                  L.jsx)(
                                                                                                      `th`,
                                                                                                      {
                                                                                                          children: `Status`,
                                                                                                      },
                                                                                                  ),
                                                                                                  (0,
                                                                                                  L.jsx)(
                                                                                                      `th`,
                                                                                                      {
                                                                                                          children: `Total`,
                                                                                                      },
                                                                                                  ),
                                                                                                  (0,
                                                                                                  L.jsxs)(
                                                                                                      `th`,
                                                                                                      {
                                                                                                          children:
                                                                                                              [
                                                                                                                  `Updated `,
                                                                                                                  (0,
                                                                                                                  L.jsx)(
                                                                                                                      m,
                                                                                                                      {
                                                                                                                          size: 12,
                                                                                                                      },
                                                                                                                  ),
                                                                                                              ],
                                                                                                      },
                                                                                                  ),
                                                                                                  (0,
                                                                                                  L.jsx)(
                                                                                                      `th`,
                                                                                                      {},
                                                                                                  ),
                                                                                              ],
                                                                                      },
                                                                                  ),
                                                                          },
                                                                      ),
                                                                      (0,
                                                                      L.jsx)(
                                                                          `tbody`,
                                                                          {
                                                                              children:
                                                                                  X.map(
                                                                                      (
                                                                                          e,
                                                                                      ) =>
                                                                                          (0,
                                                                                          L.jsxs)(
                                                                                              `tr`,
                                                                                              {
                                                                                                  children:
                                                                                                      [
                                                                                                          (0,
                                                                                                          L.jsx)(
                                                                                                              `td`,
                                                                                                              {
                                                                                                                  children:
                                                                                                                      (0,
                                                                                                                      L.jsx)(
                                                                                                                          `input`,
                                                                                                                          {
                                                                                                                              type: `checkbox`,
                                                                                                                              'aria-label':
                                                                                                                                  `Compare ` +
                                                                                                                                  e.title,
                                                                                                                              checked:
                                                                                                                                  K.includes(
                                                                                                                                      e.id,
                                                                                                                                  ),
                                                                                                                              disabled:
                                                                                                                                  K.length >=
                                                                                                                                      3 &&
                                                                                                                                  !K.includes(
                                                                                                                                      e.id,
                                                                                                                                  ),
                                                                                                                              onChange:
                                                                                                                                  () =>
                                                                                                                                      je(
                                                                                                                                          e.id,
                                                                                                                                      ),
                                                                                                                          },
                                                                                                                      ),
                                                                                                              },
                                                                                                          ),
                                                                                                          (0,
                                                                                                          L.jsxs)(
                                                                                                              `td`,
                                                                                                              {
                                                                                                                  children:
                                                                                                                      [
                                                                                                                          (0,
                                                                                                                          L.jsx)(
                                                                                                                              `button`,
                                                                                                                              {
                                                                                                                                  className: `row-link`,
                                                                                                                                  onClick:
                                                                                                                                      () =>
                                                                                                                                          Oe(
                                                                                                                                              e,
                                                                                                                                          ),
                                                                                                                                  children:
                                                                                                                                      e.title,
                                                                                                                              },
                                                                                                                          ),
                                                                                                                          (0,
                                                                                                                          L.jsxs)(
                                                                                                                              `small`,
                                                                                                                              {
                                                                                                                                  children:
                                                                                                                                      [
                                                                                                                                          e.number,
                                                                                                                                          ` · `,
                                                                                                                                          e.client ||
                                                                                                                                              e.service,
                                                                                                                                      ],
                                                                                                                              },
                                                                                                                          ),
                                                                                                                      ],
                                                                                                              },
                                                                                                          ),
                                                                                                          (0,
                                                                                                          L.jsx)(
                                                                                                              `td`,
                                                                                                              {
                                                                                                                  children:
                                                                                                                      (0,
                                                                                                                      L.jsx)(
                                                                                                                          `span`,
                                                                                                                          {
                                                                                                                              className: `company-tag`,
                                                                                                                              style: B(
                                                                                                                                  e.company,
                                                                                                                              ),
                                                                                                                              children:
                                                                                                                                  E[
                                                                                                                                      e
                                                                                                                                          .company
                                                                                                                                  ]
                                                                                                                                      .short,
                                                                                                                          },
                                                                                                                      ),
                                                                                                              },
                                                                                                          ),
                                                                                                          (0,
                                                                                                          L.jsx)(
                                                                                                              `td`,
                                                                                                              {
                                                                                                                  children:
                                                                                                                      (0,
                                                                                                                      L.jsx)(
                                                                                                                          H,
                                                                                                                          {
                                                                                                                              status: e.status,
                                                                                                                          },
                                                                                                                      ),
                                                                                                              },
                                                                                                          ),
                                                                                                          (0,
                                                                                                          L.jsx)(
                                                                                                              `td`,
                                                                                                              {
                                                                                                                  className: `amount`,
                                                                                                                  children:
                                                                                                                      M(
                                                                                                                          F(
                                                                                                                              e,
                                                                                                                          )
                                                                                                                              .total,
                                                                                                                      ),
                                                                                                              },
                                                                                                          ),
                                                                                                          (0,
                                                                                                          L.jsx)(
                                                                                                              `td`,
                                                                                                              {
                                                                                                                  children:
                                                                                                                      I(
                                                                                                                          e.updated,
                                                                                                                      ),
                                                                                                              },
                                                                                                          ),
                                                                                                          (0,
                                                                                                          L.jsx)(
                                                                                                              `td`,
                                                                                                              {
                                                                                                                  children:
                                                                                                                      (0,
                                                                                                                      L.jsx)(
                                                                                                                          `button`,
                                                                                                                          {
                                                                                                                              className: `icon-button`,
                                                                                                                              title: `Open estimate`,
                                                                                                                              onClick:
                                                                                                                                  () =>
                                                                                                                                      Oe(
                                                                                                                                          e,
                                                                                                                                      ),
                                                                                                                              children:
                                                                                                                                  (0,
                                                                                                                                  L.jsx)(
                                                                                                                                      ne,
                                                                                                                                      {
                                                                                                                                          size: 17,
                                                                                                                                      },
                                                                                                                                  ),
                                                                                                                          },
                                                                                                                      ),
                                                                                                              },
                                                                                                          ),
                                                                                                      ],
                                                                                              },
                                                                                              e.id,
                                                                                          ),
                                                                                  ),
                                                                          },
                                                                      ),
                                                                  ],
                                                              }),
                                                          }),
                                                          X.length === 0 &&
                                                              (0, L.jsxs)(
                                                                  `div`,
                                                                  {
                                                                      className: `empty`,
                                                                      children:
                                                                          [
                                                                              (0,
                                                                              L.jsx)(
                                                                                  `span`,
                                                                                  {
                                                                                      className: `empty-icon`,
                                                                                      children:
                                                                                          (0,
                                                                                          L.jsx)(
                                                                                              ie,
                                                                                              {
                                                                                                  size: 24,
                                                                                              },
                                                                                          ),
                                                                                  },
                                                                              ),
                                                                              (0,
                                                                              L.jsx)(
                                                                                  `h3`,
                                                                                  {
                                                                                      children:
                                                                                          e
                                                                                              .estimates
                                                                                              .length
                                                                                              ? `No matching estimates`
                                                                                              : `No estimates yet`,
                                                                                  },
                                                                              ),
                                                                              (0,
                                                                              L.jsx)(
                                                                                  `p`,
                                                                                  {
                                                                                      children:
                                                                                          e
                                                                                              .estimates
                                                                                              .length
                                                                                              ? `Adjust the filters to find an estimate.`
                                                                                              : `Start with a service template or build an estimate from scratch.`,
                                                                                  },
                                                                              ),
                                                                              !e
                                                                                  .estimates
                                                                                  .length &&
                                                                                  (0,
                                                                                  L.jsxs)(
                                                                                      `button`,
                                                                                      {
                                                                                          className: `button secondary compact`,
                                                                                          onClick:
                                                                                              () =>
                                                                                                  Y(
                                                                                                      `Service templates`,
                                                                                                      _,
                                                                                                  ),
                                                                                          children:
                                                                                              [
                                                                                                  `Browse templates `,
                                                                                                  (0,
                                                                                                  L.jsx)(
                                                                                                      g,
                                                                                                      {
                                                                                                          size: 15,
                                                                                                      },
                                                                                                  ),
                                                                                              ],
                                                                                      },
                                                                                  ),
                                                                          ],
                                                                  },
                                                              ),
                                                          (0, L.jsxs)(`div`, {
                                                              className: `table-foot`,
                                                              children: [
                                                                  X.length,
                                                                  ` `,
                                                                  X.length === 1
                                                                      ? `estimate`
                                                                      : `estimates`,
                                                                  (0, L.jsx)(
                                                                      `span`,
                                                                      {
                                                                          children: `USD`,
                                                                      },
                                                                  ),
                                                              ],
                                                          }),
                                                      ],
                                                  }),
                                                  (0, L.jsxs)(`div`, {
                                                      className: `catalog-heading`,
                                                      children: [
                                                          (0, L.jsxs)(`div`, {
                                                              children: [
                                                                  (0, L.jsx)(
                                                                      `span`,
                                                                      {
                                                                          className: `eyebrow`,
                                                                          children: `SERVICE CATALOG`,
                                                                      },
                                                                  ),
                                                                  (0, L.jsx)(
                                                                      `h2`,
                                                                      {
                                                                          children: `Start from a template`,
                                                                      },
                                                                  ),
                                                              ],
                                                          }),
                                                          (0, L.jsxs)(
                                                              `button`,
                                                              {
                                                                  className: `text-button`,
                                                                  onClick: () =>
                                                                      Y(
                                                                          `Service templates`,
                                                                          _,
                                                                      ),
                                                                  children: [
                                                                      `View all templates `,
                                                                      (0,
                                                                      L.jsx)(
                                                                          g,
                                                                          {
                                                                              size: 15,
                                                                          },
                                                                      ),
                                                                  ],
                                                              },
                                                          ),
                                                      ],
                                                  }),
                                                  (0, L.jsx)(`div`, {
                                                      className: `company-grid`,
                                                      children: E.map((t, n) =>
                                                          (0, L.jsxs)(
                                                              `button`,
                                                              {
                                                                  className: `panel company-card`,
                                                                  onClick: () =>
                                                                      Y(
                                                                          `Service templates`,
                                                                          n,
                                                                      ),
                                                                  children: [
                                                                      (0,
                                                                      L.jsx)(
                                                                          V,
                                                                          {
                                                                              i: n,
                                                                          },
                                                                      ),
                                                                      (0,
                                                                      L.jsxs)(
                                                                          `div`,
                                                                          {
                                                                              children:
                                                                                  [
                                                                                      (0,
                                                                                      L.jsx)(
                                                                                          `strong`,
                                                                                          {
                                                                                              children:
                                                                                                  t.short ===
                                                                                                  `Air`
                                                                                                      ? `HVAC`
                                                                                                      : t.short,
                                                                                          },
                                                                                      ),
                                                                                      (0,
                                                                                      L.jsxs)(
                                                                                          `span`,
                                                                                          {
                                                                                              children:
                                                                                                  [
                                                                                                      e.templates.filter(
                                                                                                          (
                                                                                                              e,
                                                                                                          ) =>
                                                                                                              e.company ===
                                                                                                              n,
                                                                                                      )
                                                                                                          .length,
                                                                                                      ` templates`,
                                                                                                  ],
                                                                                          },
                                                                                      ),
                                                                                  ],
                                                                          },
                                                                      ),
                                                                      (0,
                                                                      L.jsx)(
                                                                          ee,
                                                                          {
                                                                              size: 16,
                                                                          },
                                                                      ),
                                                                  ],
                                                              },
                                                              t.name,
                                                          ),
                                                      ),
                                                  }),
                                              ],
                                          })
                                        : p === `Service templates`
                                          ? (0, L.jsxs)(L.Fragment, {
                                                children: [
                                                    (0, L.jsxs)(`header`, {
                                                        className: `page-heading`,
                                                        children: [
                                                            (0, L.jsxs)(`div`, {
                                                                children: [
                                                                    (0, L.jsx)(
                                                                        `span`,
                                                                        {
                                                                            className: `eyebrow`,
                                                                            children: `SERVICE CATALOG`,
                                                                        },
                                                                    ),
                                                                    (0, L.jsx)(
                                                                        `h1`,
                                                                        {
                                                                            children: `Service templates`,
                                                                        },
                                                                    ),
                                                                    (0, L.jsx)(
                                                                        `p`,
                                                                        {
                                                                            children: `Reuse scopes and update unit rates as your pricing changes.`,
                                                                        },
                                                                    ),
                                                                ],
                                                            }),
                                                            (0, L.jsxs)(
                                                                `button`,
                                                                {
                                                                    className: `button primary`,
                                                                    onClick:
                                                                        () =>
                                                                            P({
                                                                                id: crypto.randomUUID(),
                                                                                title: ``,
                                                                                company:
                                                                                    Math.max(
                                                                                        _,
                                                                                        0,
                                                                                    ),
                                                                                service:
                                                                                    b ||
                                                                                    D[
                                                                                        [
                                                                                            0,
                                                                                            2,
                                                                                            4,
                                                                                            9,
                                                                                        ][
                                                                                            Math.max(
                                                                                                _,
                                                                                                0,
                                                                                            )
                                                                                        ]
                                                                                    ],
                                                                                scope: ``,
                                                                                terms: ``,
                                                                                lines: [
                                                                                    k(),
                                                                                ],
                                                                            }),
                                                                    children: [
                                                                        (0,
                                                                        L.jsx)(
                                                                            x,
                                                                            {
                                                                                size: 17,
                                                                            },
                                                                        ),
                                                                        ` New template`,
                                                                    ],
                                                                },
                                                            ),
                                                        ],
                                                    }),
                                                    (0, L.jsxs)(`div`, {
                                                        className: `template-filters`,
                                                        children: [
                                                            (0, L.jsxs)(
                                                                `select`,
                                                                {
                                                                    'aria-label': `Company filter`,
                                                                    value: _,
                                                                    onChange: (
                                                                        e,
                                                                    ) =>
                                                                        y(
                                                                            Number(
                                                                                e
                                                                                    .target
                                                                                    .value,
                                                                            ),
                                                                        ),
                                                                    children: [
                                                                        (0,
                                                                        L.jsx)(
                                                                            `option`,
                                                                            {
                                                                                value: -1,
                                                                                children: `All companies`,
                                                                            },
                                                                        ),
                                                                        E.map(
                                                                            (
                                                                                e,
                                                                                t,
                                                                            ) =>
                                                                                (0,
                                                                                L.jsx)(
                                                                                    `option`,
                                                                                    {
                                                                                        value: t,
                                                                                        children:
                                                                                            e.name,
                                                                                    },
                                                                                    e.name,
                                                                                ),
                                                                        ),
                                                                    ],
                                                                },
                                                            ),
                                                            (0, L.jsxs)(
                                                                `select`,
                                                                {
                                                                    'aria-label': `Service filter`,
                                                                    value: b,
                                                                    onChange: (
                                                                        e,
                                                                    ) =>
                                                                        S(
                                                                            e
                                                                                .target
                                                                                .value,
                                                                        ),
                                                                    children: [
                                                                        (0,
                                                                        L.jsx)(
                                                                            `option`,
                                                                            {
                                                                                value: ``,
                                                                                children: `All services`,
                                                                            },
                                                                        ),
                                                                        D.map(
                                                                            (
                                                                                e,
                                                                            ) =>
                                                                                (0,
                                                                                L.jsx)(
                                                                                    `option`,
                                                                                    {
                                                                                        children:
                                                                                            e,
                                                                                    },
                                                                                    e,
                                                                                ),
                                                                        ),
                                                                    ],
                                                                },
                                                            ),
                                                        ],
                                                    }),
                                                    (0, L.jsx)(`div`, {
                                                        className: `template-grid`,
                                                        children: e.templates
                                                            .filter(
                                                                (e) =>
                                                                    (_ < 0 ||
                                                                        e.company ===
                                                                            _) &&
                                                                    (!b ||
                                                                        e.service ===
                                                                            b),
                                                            )
                                                            .map((e) =>
                                                                (0, L.jsxs)(
                                                                    `article`,
                                                                    {
                                                                        className: `panel template-card`,
                                                                        children:
                                                                            [
                                                                                (0,
                                                                                L.jsxs)(
                                                                                    `div`,
                                                                                    {
                                                                                        className: `template-top`,
                                                                                        children:
                                                                                            [
                                                                                                (0,
                                                                                                L.jsx)(
                                                                                                    V,
                                                                                                    {
                                                                                                        i: e.company,
                                                                                                    },
                                                                                                ),
                                                                                                (0,
                                                                                                L.jsx)(
                                                                                                    `span`,
                                                                                                    {
                                                                                                        className: `company-tag`,
                                                                                                        style: B(
                                                                                                            e.company,
                                                                                                        ),
                                                                                                        children:
                                                                                                            e.service,
                                                                                                    },
                                                                                                ),
                                                                                            ],
                                                                                    },
                                                                                ),
                                                                                (0,
                                                                                L.jsx)(
                                                                                    `h3`,
                                                                                    {
                                                                                        children:
                                                                                            e.title,
                                                                                    },
                                                                                ),
                                                                                (0,
                                                                                L.jsx)(
                                                                                    `p`,
                                                                                    {
                                                                                        children:
                                                                                            e.scope ||
                                                                                            `Custom service template.`,
                                                                                    },
                                                                                ),
                                                                                (0,
                                                                                L.jsxs)(
                                                                                    `div`,
                                                                                    {
                                                                                        className: `template-actions`,
                                                                                        children:
                                                                                            [
                                                                                                (0,
                                                                                                L.jsx)(
                                                                                                    `button`,
                                                                                                    {
                                                                                                        className: `button secondary compact`,
                                                                                                        onClick:
                                                                                                            () =>
                                                                                                                P(
                                                                                                                    structuredClone(
                                                                                                                        e,
                                                                                                                    ),
                                                                                                                ),
                                                                                                        children: `Edit rates`,
                                                                                                    },
                                                                                                ),
                                                                                                (0,
                                                                                                L.jsxs)(
                                                                                                    `button`,
                                                                                                    {
                                                                                                        className: `text-button`,
                                                                                                        onClick:
                                                                                                            () =>
                                                                                                                De(
                                                                                                                    e,
                                                                                                                ),
                                                                                                        children:
                                                                                                            [
                                                                                                                `Use template `,
                                                                                                                (0,
                                                                                                                L.jsx)(
                                                                                                                    g,
                                                                                                                    {
                                                                                                                        size: 15,
                                                                                                                    },
                                                                                                                ),
                                                                                                            ],
                                                                                                    },
                                                                                                ),
                                                                                            ],
                                                                                    },
                                                                                ),
                                                                            ],
                                                                    },
                                                                    e.id,
                                                                ),
                                                            ),
                                                    }),
                                                ],
                                            })
                                          : p === `Compare estimates`
                                            ? (0, L.jsxs)(L.Fragment, {
                                                  children: [
                                                      (0, L.jsx)(`header`, {
                                                          className: `page-heading`,
                                                          children: (0, L.jsxs)(
                                                              `div`,
                                                              {
                                                                  children: [
                                                                      (0,
                                                                      L.jsx)(
                                                                          `span`,
                                                                          {
                                                                              className: `eyebrow`,
                                                                              children: `PRICING REVIEW`,
                                                                          },
                                                                      ),
                                                                      (0,
                                                                      L.jsx)(
                                                                          `h1`,
                                                                          {
                                                                              children: `Compare estimates`,
                                                                          },
                                                                      ),
                                                                      (0,
                                                                      L.jsx)(
                                                                          `p`,
                                                                          {
                                                                              children: `Compare up to three saved estimates. Duplicate an estimate to price another option.`,
                                                                          },
                                                                      ),
                                                                  ],
                                                              },
                                                          ),
                                                      }),
                                                      (0, L.jsxs)(`section`, {
                                                          className: `panel section`,
                                                          children: [
                                                              (0, L.jsx)(`h3`, {
                                                                  children: `Choose estimates`,
                                                              }),
                                                              e.estimates.length
                                                                  ? (0, L.jsx)(
                                                                        `div`,
                                                                        {
                                                                            className: `pick-list`,
                                                                            children:
                                                                                e.estimates.map(
                                                                                    (
                                                                                        e,
                                                                                    ) =>
                                                                                        (0,
                                                                                        L.jsxs)(
                                                                                            `label`,
                                                                                            {
                                                                                                children:
                                                                                                    [
                                                                                                        (0,
                                                                                                        L.jsx)(
                                                                                                            `input`,
                                                                                                            {
                                                                                                                type: `checkbox`,
                                                                                                                checked:
                                                                                                                    K.includes(
                                                                                                                        e.id,
                                                                                                                    ),
                                                                                                                disabled:
                                                                                                                    K.length >=
                                                                                                                        3 &&
                                                                                                                    !K.includes(
                                                                                                                        e.id,
                                                                                                                    ),
                                                                                                                onChange:
                                                                                                                    () =>
                                                                                                                        je(
                                                                                                                            e.id,
                                                                                                                        ),
                                                                                                            },
                                                                                                        ),
                                                                                                        e.title,
                                                                                                        ` `,
                                                                                                        (0,
                                                                                                        L.jsx)(
                                                                                                            `small`,
                                                                                                            {
                                                                                                                children:
                                                                                                                    e.number,
                                                                                                            },
                                                                                                        ),
                                                                                                    ],
                                                                                            },
                                                                                            e.id,
                                                                                        ),
                                                                                ),
                                                                        },
                                                                    )
                                                                  : (0, L.jsx)(
                                                                        `p`,
                                                                        {
                                                                            className: `muted`,
                                                                            children: `Save an estimate to start a comparison.`,
                                                                        },
                                                                    ),
                                                          ],
                                                      }),
                                                      Z.length > 0 &&
                                                          (0, L.jsx)(
                                                              `section`,
                                                              {
                                                                  className: `panel section compare-panel`,
                                                                  children: (0,
                                                                  L.jsx)(
                                                                      `div`,
                                                                      {
                                                                          className: `scroll`,
                                                                          children:
                                                                              (0,
                                                                              L.jsxs)(
                                                                                  `table`,
                                                                                  {
                                                                                      children:
                                                                                          [
                                                                                              (0,
                                                                                              L.jsx)(
                                                                                                  `thead`,
                                                                                                  {
                                                                                                      children:
                                                                                                          (0,
                                                                                                          L.jsxs)(
                                                                                                              `tr`,
                                                                                                              {
                                                                                                                  children:
                                                                                                                      [
                                                                                                                          (0,
                                                                                                                          L.jsx)(
                                                                                                                              `th`,
                                                                                                                              {
                                                                                                                                  children: `Pricing metric`,
                                                                                                                              },
                                                                                                                          ),
                                                                                                                          Z.map(
                                                                                                                              (
                                                                                                                                  e,
                                                                                                                              ) =>
                                                                                                                                  (0,
                                                                                                                                  L.jsxs)(
                                                                                                                                      `th`,
                                                                                                                                      {
                                                                                                                                          children:
                                                                                                                                              [
                                                                                                                                                  e.title,
                                                                                                                                                  (0,
                                                                                                                                                  L.jsx)(
                                                                                                                                                      `small`,
                                                                                                                                                      {
                                                                                                                                                          children:
                                                                                                                                                              e.number,
                                                                                                                                                      },
                                                                                                                                                  ),
                                                                                                                                              ],
                                                                                                                                      },
                                                                                                                                      e.id,
                                                                                                                                  ),
                                                                                                                          ),
                                                                                                                      ],
                                                                                                              },
                                                                                                          ),
                                                                                                  },
                                                                                              ),
                                                                                              (0,
                                                                                              L.jsxs)(
                                                                                                  `tbody`,
                                                                                                  {
                                                                                                      children:
                                                                                                          [
                                                                                                              [
                                                                                                                  [
                                                                                                                      `Company`,
                                                                                                                      (
                                                                                                                          e,
                                                                                                                      ) =>
                                                                                                                          E[
                                                                                                                              e
                                                                                                                                  .company
                                                                                                                          ]
                                                                                                                              .short,
                                                                                                                  ],
                                                                                                                  [
                                                                                                                      `Service`,
                                                                                                                      (
                                                                                                                          e,
                                                                                                                      ) =>
                                                                                                                          e.service,
                                                                                                                  ],
                                                                                                                  [
                                                                                                                      `Approval`,
                                                                                                                      (
                                                                                                                          e,
                                                                                                                      ) =>
                                                                                                                          e.status,
                                                                                                                  ],
                                                                                                                  [
                                                                                                                      `Direct cost`,
                                                                                                                      (
                                                                                                                          e,
                                                                                                                      ) =>
                                                                                                                          M(
                                                                                                                              F(
                                                                                                                                  e,
                                                                                                                              )
                                                                                                                                  .cost,
                                                                                                                          ),
                                                                                                                  ],
                                                                                                                  [
                                                                                                                      `Markup`,
                                                                                                                      (
                                                                                                                          e,
                                                                                                                      ) =>
                                                                                                                          e
                                                                                                                              .pricing
                                                                                                                              .markup +
                                                                                                                          `%`,
                                                                                                                  ],
                                                                                                                  [
                                                                                                                      `Discount`,
                                                                                                                      (
                                                                                                                          e,
                                                                                                                      ) =>
                                                                                                                          M(
                                                                                                                              F(
                                                                                                                                  e,
                                                                                                                              )
                                                                                                                                  .discount,
                                                                                                                          ),
                                                                                                                  ],
                                                                                                                  [
                                                                                                                      `Trip charge`,
                                                                                                                      (
                                                                                                                          e,
                                                                                                                      ) =>
                                                                                                                          M(
                                                                                                                              e
                                                                                                                                  .pricing
                                                                                                                                  .trip,
                                                                                                                          ),
                                                                                                                  ],
                                                                                                                  [
                                                                                                                      `Total`,
                                                                                                                      (
                                                                                                                          e,
                                                                                                                      ) =>
                                                                                                                          M(
                                                                                                                              F(
                                                                                                                                  e,
                                                                                                                              )
                                                                                                                                  .total,
                                                                                                                          ),
                                                                                                                  ],
                                                                                                                  [
                                                                                                                      `Gross margin`,
                                                                                                                      (
                                                                                                                          e,
                                                                                                                      ) =>
                                                                                                                          F(
                                                                                                                              e,
                                                                                                                          )
                                                                                                                              .incomplete ||
                                                                                                                          !F(
                                                                                                                              e,
                                                                                                                          )
                                                                                                                              .total
                                                                                                                              ? `Needs pricing`
                                                                                                                              : F(
                                                                                                                                    e,
                                                                                                                                ).margin.toFixed(
                                                                                                                                    1,
                                                                                                                                ) +
                                                                                                                                `%`,
                                                                                                                  ],
                                                                                                                  [
                                                                                                                      `Margin target`,
                                                                                                                      (
                                                                                                                          e,
                                                                                                                      ) =>
                                                                                                                          e
                                                                                                                              .pricing
                                                                                                                              .target +
                                                                                                                          `%`,
                                                                                                                  ],
                                                                                                              ].map(
                                                                                                                  ([
                                                                                                                      e,
                                                                                                                      t,
                                                                                                                  ]) =>
                                                                                                                      (0,
                                                                                                                      L.jsxs)(
                                                                                                                          `tr`,
                                                                                                                          {
                                                                                                                              children:
                                                                                                                                  [
                                                                                                                                      (0,
                                                                                                                                      L.jsx)(
                                                                                                                                          `th`,
                                                                                                                                          {
                                                                                                                                              children:
                                                                                                                                                  String(
                                                                                                                                                      e,
                                                                                                                                                  ),
                                                                                                                                          },
                                                                                                                                      ),
                                                                                                                                      Z.map(
                                                                                                                                          (
                                                                                                                                              e,
                                                                                                                                          ) =>
                                                                                                                                              (0,
                                                                                                                                              L.jsx)(
                                                                                                                                                  `td`,
                                                                                                                                                  {
                                                                                                                                                      children:
                                                                                                                                                          t(
                                                                                                                                                              e,
                                                                                                                                                          ),
                                                                                                                                                  },
                                                                                                                                                  e.id,
                                                                                                                                              ),
                                                                                                                                      ),
                                                                                                                                  ],
                                                                                                                          },
                                                                                                                          String(
                                                                                                                              e,
                                                                                                                          ),
                                                                                                                      ),
                                                                                                              ),
                                                                                                              (0,
                                                                                                              L.jsxs)(
                                                                                                                  `tr`,
                                                                                                                  {
                                                                                                                      children:
                                                                                                                          [
                                                                                                                              (0,
                                                                                                                              L.jsx)(
                                                                                                                                  `th`,
                                                                                                                                  {
                                                                                                                                      children: `Options`,
                                                                                                                                  },
                                                                                                                              ),
                                                                                                                              Z.map(
                                                                                                                                  (
                                                                                                                                      e,
                                                                                                                                  ) =>
                                                                                                                                      (0,
                                                                                                                                      L.jsxs)(
                                                                                                                                          `td`,
                                                                                                                                          {
                                                                                                                                              children:
                                                                                                                                                  [
                                                                                                                                                      (0,
                                                                                                                                                      L.jsx)(
                                                                                                                                                          `button`,
                                                                                                                                                          {
                                                                                                                                                              className: `text-button`,
                                                                                                                                                              onClick:
                                                                                                                                                                  () =>
                                                                                                                                                                      Oe(
                                                                                                                                                                          e,
                                                                                                                                                                      ),
                                                                                                                                                              children: `Open`,
                                                                                                                                                          },
                                                                                                                                                      ),
                                                                                                                                                      (0,
                                                                                                                                                      L.jsx)(
                                                                                                                                                          `button`,
                                                                                                                                                          {
                                                                                                                                                              className: `button secondary compact`,
                                                                                                                                                              onClick:
                                                                                                                                                                  () => {
                                                                                                                                                                      let {
                                                                                                                                                                          history:
                                                                                                                                                                              t,
                                                                                                                                                                          ...n
                                                                                                                                                                      } =
                                                                                                                                                                          e;
                                                                                                                                                                      De(
                                                                                                                                                                          void 0,
                                                                                                                                                                          n,
                                                                                                                                                                      );
                                                                                                                                                                  },
                                                                                                                                                              children: `Duplicate`,
                                                                                                                                                          },
                                                                                                                                                      ),
                                                                                                                                                  ],
                                                                                                                                          },
                                                                                                                                          e.id,
                                                                                                                                      ),
                                                                                                                              ),
                                                                                                                          ],
                                                                                                                  },
                                                                                                              ),
                                                                                                          ],
                                                                                                  },
                                                                                              ),
                                                                                          ],
                                                                                  },
                                                                              ),
                                                                      },
                                                                  ),
                                                              },
                                                          ),
                                                  ],
                                              })
                                            : p === `Calculator`
                                              ? (0, L.jsxs)(L.Fragment, {
                                                    children: [
                                                        (0, L.jsx)(`header`, {
                                                            className: `page-heading`,
                                                            children: (0,
                                                            L.jsxs)(`div`, {
                                                                children: [
                                                                    (0, L.jsx)(
                                                                        `span`,
                                                                        {
                                                                            className: `eyebrow`,
                                                                            children: `QUICK TOOLS`,
                                                                        },
                                                                    ),
                                                                    (0, L.jsx)(
                                                                        `h1`,
                                                                        {
                                                                            children: `Quantity calculator`,
                                                                        },
                                                                    ),
                                                                    (0, L.jsx)(
                                                                        `p`,
                                                                        {
                                                                            children: `Calculate a line total with optional waste or overage.`,
                                                                        },
                                                                    ),
                                                                ],
                                                            }),
                                                        }),
                                                        (0, L.jsxs)(`section`, {
                                                            className: `panel section calculator-panel`,
                                                            children: [
                                                                (0, L.jsx)(
                                                                    `h3`,
                                                                    {
                                                                        children: `Quantity and unit cost`,
                                                                    },
                                                                ),
                                                                (0, L.jsxs)(
                                                                    `div`,
                                                                    {
                                                                        className: `fields`,
                                                                        children:
                                                                            [
                                                                                (0,
                                                                                L.jsxs)(
                                                                                    `label`,
                                                                                    {
                                                                                        children:
                                                                                            [
                                                                                                `Base quantity`,
                                                                                                (0,
                                                                                                L.jsx)(
                                                                                                    U,
                                                                                                    {
                                                                                                        label: `Base quantity`,
                                                                                                        value: q.qty,
                                                                                                        change: (
                                                                                                            e,
                                                                                                        ) =>
                                                                                                            J(
                                                                                                                {
                                                                                                                    ...q,
                                                                                                                    qty:
                                                                                                                        e ??
                                                                                                                        0,
                                                                                                                },
                                                                                                            ),
                                                                                                    },
                                                                                                ),
                                                                                            ],
                                                                                    },
                                                                                ),
                                                                                (0,
                                                                                L.jsxs)(
                                                                                    `label`,
                                                                                    {
                                                                                        children:
                                                                                            [
                                                                                                `Unit cost ($)`,
                                                                                                (0,
                                                                                                L.jsx)(
                                                                                                    U,
                                                                                                    {
                                                                                                        label: `Unit cost`,
                                                                                                        value: q.price,
                                                                                                        change: (
                                                                                                            e,
                                                                                                        ) =>
                                                                                                            J(
                                                                                                                {
                                                                                                                    ...q,
                                                                                                                    price:
                                                                                                                        e ??
                                                                                                                        0,
                                                                                                                },
                                                                                                            ),
                                                                                                    },
                                                                                                ),
                                                                                            ],
                                                                                    },
                                                                                ),
                                                                                (0,
                                                                                L.jsxs)(
                                                                                    `label`,
                                                                                    {
                                                                                        children:
                                                                                            [
                                                                                                `Waste or overage (%)`,
                                                                                                (0,
                                                                                                L.jsx)(
                                                                                                    U,
                                                                                                    {
                                                                                                        label: `Waste or overage`,
                                                                                                        value: q.extra,
                                                                                                        change: (
                                                                                                            e,
                                                                                                        ) =>
                                                                                                            J(
                                                                                                                {
                                                                                                                    ...q,
                                                                                                                    extra:
                                                                                                                        e ??
                                                                                                                        0,
                                                                                                                },
                                                                                                            ),
                                                                                                    },
                                                                                                ),
                                                                                            ],
                                                                                    },
                                                                                ),
                                                                            ],
                                                                    },
                                                                ),
                                                                (0, L.jsxs)(
                                                                    `dl`,
                                                                    {
                                                                        children:
                                                                            [
                                                                                (0,
                                                                                L.jsx)(
                                                                                    `dt`,
                                                                                    {
                                                                                        children: `Adjusted quantity`,
                                                                                    },
                                                                                ),
                                                                                (0,
                                                                                L.jsx)(
                                                                                    `dd`,
                                                                                    {
                                                                                        children:
                                                                                            N(
                                                                                                q.qty *
                                                                                                    (1 +
                                                                                                        q.extra /
                                                                                                            100),
                                                                                            ).toLocaleString(),
                                                                                    },
                                                                                ),
                                                                            ],
                                                                    },
                                                                ),
                                                                (0, L.jsxs)(
                                                                    `div`,
                                                                    {
                                                                        className: `total-row`,
                                                                        children:
                                                                            [
                                                                                (0,
                                                                                L.jsx)(
                                                                                    `span`,
                                                                                    {
                                                                                        children: `Calculated total`,
                                                                                    },
                                                                                ),
                                                                                (0,
                                                                                L.jsx)(
                                                                                    `strong`,
                                                                                    {
                                                                                        children:
                                                                                            M(
                                                                                                N(
                                                                                                    q.qty *
                                                                                                        (1 +
                                                                                                            q.extra /
                                                                                                                100) *
                                                                                                        q.price,
                                                                                                ),
                                                                                            ),
                                                                                    },
                                                                                ),
                                                                            ],
                                                                    },
                                                                ),
                                                            ],
                                                        }),
                                                    ],
                                                })
                                              : (0, L.jsxs)(L.Fragment, {
                                                    children: [
                                                        (0, L.jsxs)(`header`, {
                                                            className: `page-heading`,
                                                            children: [
                                                                (0, L.jsxs)(
                                                                    `div`,
                                                                    {
                                                                        children:
                                                                            [
                                                                                (0,
                                                                                L.jsx)(
                                                                                    `span`,
                                                                                    {
                                                                                        className: `eyebrow`,
                                                                                        children: `WORKSPACE SETTINGS`,
                                                                                    },
                                                                                ),
                                                                                (0,
                                                                                L.jsx)(
                                                                                    `h1`,
                                                                                    {
                                                                                        children: `Pricing defaults`,
                                                                                    },
                                                                                ),
                                                                                (0,
                                                                                L.jsx)(
                                                                                    `p`,
                                                                                    {
                                                                                        children: `Set starting values for new estimates across all four companies.`,
                                                                                    },
                                                                                ),
                                                                            ],
                                                                    },
                                                                ),
                                                                (0, L.jsxs)(
                                                                    `button`,
                                                                    {
                                                                        className: `button primary`,
                                                                        disabled:
                                                                            o,
                                                                        onClick:
                                                                            () =>
                                                                                Ee(
                                                                                    {
                                                                                        ...e,
                                                                                        defaults:
                                                                                            R,
                                                                                    },
                                                                                    `Pricing defaults saved.`,
                                                                                ),
                                                                        children:
                                                                            [
                                                                                (0,
                                                                                L.jsx)(
                                                                                    v,
                                                                                    {
                                                                                        size: 16,
                                                                                    },
                                                                                ),
                                                                                ` Save defaults`,
                                                                            ],
                                                                    },
                                                                ),
                                                            ],
                                                        }),
                                                        (0, L.jsxs)(`section`, {
                                                            className: `panel section`,
                                                            children: [
                                                                (0, L.jsx)(
                                                                    `h3`,
                                                                    {
                                                                        children: `Default rates and thresholds`,
                                                                    },
                                                                ),
                                                                (0, L.jsx)(
                                                                    `p`,
                                                                    {
                                                                        className: `muted`,
                                                                        children: `Saved estimates keep their pricing. You can override these defaults on any estimate.`,
                                                                    },
                                                                ),
                                                                (0, L.jsx)(he, {
                                                                    value: R,
                                                                    change: z,
                                                                }),
                                                                (0, L.jsx)(
                                                                    `p`,
                                                                    {
                                                                        className: `muted policy-note`,
                                                                        children: `Discount applies to the line subtotal. The trip charge is added afterward. Authorization thresholds are shown as flags on each estimate.`,
                                                                    },
                                                                ),
                                                            ],
                                                        }),
                                                    ],
                                                })
                                : (0, L.jsxs)(`section`, {
                                      className: `panel empty`,
                                      children: [
                                          (0, L.jsx)(re, { size: 26 }),
                                          (0, L.jsx)(`h3`, {
                                              children: d
                                                  ? `Workspace unavailable`
                                                  : `Loading your workspace`,
                                          }),
                                          d &&
                                              (0, L.jsx)(`button`, {
                                                  className: `button secondary`,
                                                  onClick: () =>
                                                      location.reload(),
                                                  children: `Reload`,
                                              }),
                                      ],
                                  }),
                        ],
                    }),
                    (0, L.jsxs)(`footer`, {
                        className: `main-footer`,
                        children: [
                            (0, L.jsx)(`span`, {
                                children: `Crystal Creek Estimating Desk`,
                            }),
                            (0, L.jsxs)(`span`, {
                                children: [
                                    `Greater Houston, TX `,
                                    (0, L.jsx)(`span`, {
                                        className: `footer-dot`,
                                        children: `•`,
                                    }),
                                    ` USD`,
                                ],
                            }),
                        ],
                    }),
                ],
            }),
        ],
    });
}
export { ye as default };
