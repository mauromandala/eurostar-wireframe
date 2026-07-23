/* @ds-bundle: {"format":4,"namespace":"EurostarDesignSystem_8ffca8","components":[{"name":"Button","sourcePath":"components/core/Button.jsx"},{"name":"MachineCard","sourcePath":"components/core/MachineCard.jsx"},{"name":"SectionKicker","sourcePath":"components/core/SectionKicker.jsx"},{"name":"SpecRow","sourcePath":"components/core/SpecRow.jsx"},{"name":"SpecList","sourcePath":"components/core/SpecRow.jsx"},{"name":"StatBlock","sourcePath":"components/core/StatBlock.jsx"},{"name":"Tag","sourcePath":"components/core/Tag.jsx"}],"sourceHashes":{"components/core/Button.jsx":"9bc17febcbbc","components/core/MachineCard.jsx":"9b456049645e","components/core/SectionKicker.jsx":"ad7dcbe59053","components/core/SpecRow.jsx":"26535d3b94d4","components/core/StatBlock.jsx":"c5b7a807238a","components/core/Tag.jsx":"d16f2e451b6b","ui_kits/marketing-site/Home.jsx":"5daf75fa294f","ui_kits/marketing-site/MachinesFilling.jsx":"fc9aca54f580","ui_kits/marketing-site/MecLd.jsx":"cc6286d2d80b","ui_kits/marketing-site/Nav.jsx":"42ee1fd30800"},"inlinedExternals":[],"unexposedExports":[]} */

(() => {

const __ds_ns = (window.EurostarDesignSystem_8ffca8 = window.EurostarDesignSystem_8ffca8 || {});

const __ds_scope = {};

(__ds_ns.__errors = __ds_ns.__errors || []);

// components/core/Button.jsx
try { (() => {
const sizes = {
  md: {
    padding: '11px 20px',
    font: '12px'
  },
  lg: {
    padding: '16px 30px',
    font: '14px'
  }
};
function Button({
  variant = 'primary',
  size = 'lg',
  dark = false,
  icon,
  children,
  href,
  onClick,
  style
}) {
  const s = sizes[size] || sizes.lg;
  const base = {
    display: 'inline-flex',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 10,
    fontFamily: 'var(--font-display)',
    fontWeight: 700,
    fontSize: s.font,
    letterSpacing: '0.08em',
    textTransform: 'uppercase',
    textDecoration: 'none',
    padding: s.padding,
    cursor: 'pointer',
    border: 'none',
    transition: `background var(--duration-base) var(--ease-standard), color var(--duration-base) var(--ease-standard), transform var(--duration-base) var(--ease-standard)`,
    clipPath: 'none',
    ...style
  };
  let variantStyle = {};
  if (variant === 'primary') variantStyle = dark ? {
    background: '#fff',
    color: 'var(--text-heading)'
  } : {
    background: 'var(--action-primary)',
    color: '#fff'
  };else if (variant === 'ghost') variantStyle = dark ? {
    background: 'rgba(255,255,255,0.1)',
    color: '#fff'
  } : {
    background: 'rgba(0,0,0,0.08)',
    color: 'var(--text-heading)'
  };else if (variant === 'text') variantStyle = {
    background: 'transparent',
    clipPath: 'none',
    color: dark ? '#fff' : 'var(--text-heading)',
    padding: s.padding.replace(/^\S+/, '0')
  };
  const Tag = href ? 'a' : 'button';
  return React.createElement(Tag, {
    href,
    onClick,
    style: {
      ...base,
      ...variantStyle
    }
  }, icon, children);
}
Object.assign(__ds_scope, { Button });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/core/Button.jsx", error: String((e && e.message) || e) }); }

// components/core/SectionKicker.jsx
try { (() => {
function SectionKicker({
  children,
  dark = false,
  align = 'left'
}) {
  return React.createElement('p', {
    style: {
      margin: 0,
      display: 'flex',
      alignItems: 'center',
      justifyContent: align === 'right' ? 'flex-end' : align === 'center' ? 'center' : 'flex-start',
      fontFamily: 'var(--font-display)',
      fontWeight: 600,
      fontSize: 13,
      letterSpacing: 'var(--tracking-kicker)',
      textTransform: 'uppercase',
      color: dark ? 'var(--text-accent-on-dark)' : 'var(--text-accent)'
    }
  }, children);
}
Object.assign(__ds_scope, { SectionKicker });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/core/SectionKicker.jsx", error: String((e && e.message) || e) }); }

// components/core/SpecRow.jsx
try { (() => {
function SpecRow({
  label,
  children
}) {
  return React.createElement(React.Fragment, null, React.createElement('dt', {
    style: {
      padding: '15px 14px 15px 0',
      fontFamily: 'var(--font-body)',
      fontWeight: 500,
      fontSize: 11,
      letterSpacing: '0.16em',
      color: 'var(--blue-500)',
      textTransform: 'uppercase',
      alignSelf: 'center'
    }
  }, label), React.createElement('dd', {
    style: {
      margin: 0,
      padding: '15px 0',
      fontFamily: 'var(--font-body)',
      fontWeight: 400,
      fontSize: 15,
      lineHeight: 1.5,
      color: 'var(--text-heading)',
      alignSelf: 'center'
    }
  }, children));
}
function SpecList({
  children
}) {
  return React.createElement('dl', {
    style: {
      display: 'grid',
      gridTemplateColumns: '190px 1fr',
      margin: 0
    }
  }, React.Children.map(children, c => React.cloneElement(c, {})));
}
Object.assign(__ds_scope, { SpecRow, SpecList });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/core/SpecRow.jsx", error: String((e && e.message) || e) }); }

// components/core/StatBlock.jsx
try { (() => {
function StatBlock({
  value,
  label,
  dark = true,
  size = 'lg'
}) {
  return React.createElement('div', null, React.createElement('div', {
    style: {
      fontFamily: 'var(--font-display)',
      fontWeight: 900,
      fontSize: size === 'lg' ? 'clamp(40px,5vw,74px)' : 'clamp(22px,2.2vw,30px)',
      lineHeight: size === 'lg' ? 0.9 : 1,
      color: dark ? '#fff' : 'var(--text-heading)'
    }
  }, value), React.createElement('div', {
    style: {
      marginTop: size === 'lg' ? 10 : 0,
      fontFamily: 'var(--font-body)',
      fontWeight: 400,
      fontSize: size === 'lg' ? 12 : 11,
      letterSpacing: size === 'lg' ? '0.14em' : '0.06em',
      textTransform: 'uppercase',
      color: dark ? 'var(--text-accent-on-dark)' : 'var(--gray-500)'
    }
  }, label));
}
Object.assign(__ds_scope, { StatBlock });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/core/StatBlock.jsx", error: String((e && e.message) || e) }); }

// components/core/MachineCard.jsx
try { (() => {
function MachineCard({
  image,
  name,
  subtitle,
  stat1,
  stat2,
  href = '#'
}) {
  return React.createElement('a', {
    href,
    style: {
      display: 'flex',
      flexDirection: 'column',
      textDecoration: 'none',
      background: 'var(--surface-card)'
    }
  }, React.createElement('div', {
    style: {
      overflow: 'hidden',
      aspectRatio: '4/3.2',
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
      background: 'var(--gray-150)',
      border: '1px dashed var(--gray-400)'
    },
    'aria-hidden': 'true'
  }, React.createElement('span', {
    style: {
      fontFamily: 'var(--font-body)',
      fontSize: 12,
      letterSpacing: '.04em',
      color: 'var(--gray-500)',
      textAlign: 'center',
      padding: '0 12px'
    }
  }, `[IMG: ${name}]`)), React.createElement('div', {
    style: {
      padding: '20px 22px 0'
    }
  }, React.createElement('h3', {
    style: {
      margin: '0 0 4px',
      fontFamily: 'var(--font-display)',
      fontWeight: 800,
      fontSize: 'clamp(22px,2vw,28px)',
      color: 'var(--text-heading)',
      textTransform: 'uppercase'
    }
  }, name), React.createElement('p', {
    style: {
      margin: 0,
      fontFamily: 'var(--font-body)',
      fontWeight: 500,
      fontSize: 14,
      color: 'var(--action-primary)'
    }
  }, subtitle)), React.createElement('div', {
    style: {
      display: 'flex',
      padding: '18px 22px 22px',
      marginTop: 14,
      borderTop: '1px solid var(--border-subtle)',
      gap: 24
    }
  }, React.createElement('div', {
    style: {
      flex: 1
    }
  }, React.createElement(__ds_scope.StatBlock, {
    value: stat1.value,
    label: stat1.label,
    dark: false,
    size: 'sm'
  })), React.createElement('div', {
    style: {
      flex: 1
    }
  }, React.createElement(__ds_scope.StatBlock, {
    value: stat2.value,
    label: stat2.label,
    dark: false,
    size: 'sm'
  }))));
}
Object.assign(__ds_scope, { MachineCard });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/core/MachineCard.jsx", error: String((e && e.message) || e) }); }

// components/core/Tag.jsx
try { (() => {
function Tag({
  children,
  icon,
  dark = false
}) {
  return React.createElement('span', {
    style: {
      display: 'inline-flex',
      alignItems: 'center',
      gap: 8,
      padding: '10px 16px',
      border: `1px solid ${dark ? 'var(--border-on-dark)' : 'var(--border-subtle)'}`,
      fontFamily: 'var(--font-body)',
      fontWeight: 400,
      fontSize: 13,
      color: dark ? 'var(--text-on-dark)' : 'var(--gray-700)'
    }
  }, icon, children);
}
Object.assign(__ds_scope, { Tag });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/core/Tag.jsx", error: String((e && e.message) || e) }); }

// ui_kits/marketing-site/Home.jsx
try { (() => {
const {
  SectionKicker,
  Button,
  MachineCard,
  StatBlock
} = window.EurostarDesignSystem_8ffca8;
const sectors = [['Wine', 'sec-wine.png'], ['Spirits', 'sec-spirits.png'], ['Beer', 'sec-beer.png'], ['Water & soft drinks', 'sec-water.png'], ['Juices', 'sec-juices.png'], ['Oil & vinegar', 'sec-oil.png']];
function Home({
  goto
}) {
  return React.createElement('div', null, React.createElement('section', {
    style: {
      position: 'relative',
      minHeight: '78vh',
      display: 'flex',
      alignItems: 'center',
      background: 'var(--surface-dark)',
      overflow: 'hidden'
    }
  }, React.createElement('img', {
    src: '../../assets/hub-filling-hero.png',
    alt: '',
    style: {
      position: 'absolute',
      inset: 0,
      width: '100%',
      height: '100%',
      objectFit: 'cover',
      opacity: 0.55
    }
  }), React.createElement('div', {
    style: {
      position: 'absolute',
      inset: 0,
      background: 'linear-gradient(90deg, rgba(20,33,63,.92) 0%, rgba(20,33,63,.5) 70%)'
    }
  }), React.createElement('div', {
    style: {
      position: 'relative',
      maxWidth: 1200,
      margin: '0 auto',
      padding: '0 clamp(20px,6vw,80px)'
    }
  }, React.createElement(SectionKicker, {
    dark: true
  }, 'Bottling and packaging solutions, since 1996'), React.createElement('h1', {
    style: {
      margin: '22px 0 0',
      font: 'var(--text-hero)',
      color: '#fff',
      textTransform: 'uppercase',
      maxWidth: '14ch'
    }
  }, 'Enjoy the filling.'), React.createElement('p', {
    style: {
      margin: '28px 0 0',
      font: 'var(--text-body-lg)',
      color: 'rgba(255,255,255,.85)',
      maxWidth: 540
    }
  }, 'We build the bottling and packaging machines that fill the world — engineered, assembled and perfected in the heart of Piedmont.'), React.createElement('div', {
    style: {
      marginTop: 36
    }
  }, React.createElement(Button, {
    variant: 'primary',
    onClick: () => goto('filling')
  }, 'Explore machines')))), React.createElement('section', {
    style: {
      background: 'var(--surface-dark)',
      padding: 'var(--space-section-y) var(--space-section-x)'
    }
  }, React.createElement('div', {
    style: {
      maxWidth: 1360,
      margin: '0 auto'
    }
  }, React.createElement(SectionKicker, {
    dark: true
  }, 'Industries'), React.createElement('h2', {
    style: {
      margin: '16px 0 32px',
      font: 'var(--text-display-2)',
      color: '#fff',
      textTransform: 'uppercase'
    }
  }, 'What do you bottle?'), React.createElement('div', {
    style: {
      display: 'grid',
      gridTemplateColumns: 'repeat(auto-fit,minmax(180px,1fr))',
      gap: 16
    }
  }, sectors.map(([label, img]) => React.createElement('div', {
    key: label,
    style: {
      position: 'relative',
      aspectRatio: '4/5',
      overflow: 'hidden',
      background: '#0B1330'
    }
  }, React.createElement('img', {
    src: '../../assets/' + img,
    alt: label,
    style: {
      position: 'absolute',
      inset: 0,
      width: '100%',
      height: '100%',
      objectFit: 'cover'
    }
  }), React.createElement('div', {
    style: {
      position: 'absolute',
      inset: 0,
      background: 'linear-gradient(135deg,rgba(255,255,255,.9) 0%,rgba(255,255,255,.4) 30%,transparent 55%)'
    }
  }), React.createElement('span', {
    style: {
      position: 'absolute',
      left: 14,
      top: 14,
      font: 'var(--text-h3)',
      color: 'var(--action-primary)',
      fontSize: 20,
      textTransform: 'uppercase'
    }
  }, label)))))), React.createElement('section', {
    style: {
      background: '#fff',
      padding: 'var(--space-section-y) var(--space-section-x)'
    }
  }, React.createElement('div', {
    style: {
      maxWidth: 1240,
      margin: '0 auto',
      display: 'grid',
      gridTemplateColumns: 'repeat(4,1fr)',
      gap: 24,
      textAlign: 'center'
    }
  }, React.createElement(StatBlock, {
    value: '30',
    label: 'Years in business',
    dark: false,
    size: 'lg'
  }), React.createElement(StatBlock, {
    value: '2,000+',
    label: 'Machines installed',
    dark: false,
    size: 'lg'
  }), React.createElement(StatBlock, {
    value: '60+',
    label: 'Countries served',
    dark: false,
    size: 'lg'
  }), React.createElement(StatBlock, {
    value: '24/7',
    label: 'Support response',
    dark: false,
    size: 'lg'
  }))), React.createElement('section', {
    style: {
      background: 'var(--surface-tint)',
      padding: 'var(--space-section-y) var(--space-section-x)'
    }
  }, React.createElement('div', {
    style: {
      maxWidth: 1360,
      margin: '0 auto'
    }
  }, React.createElement(SectionKicker, null, 'Machines'), React.createElement('h2', {
    style: {
      margin: '16px 0 32px',
      font: 'var(--text-display-2)',
      color: 'var(--text-heading)',
      textTransform: 'uppercase'
    }
  }, 'The filling range'), React.createElement('div', {
    style: {
      display: 'grid',
      gridTemplateColumns: 'repeat(auto-fit,minmax(240px,1fr))',
      gap: 24
    }
  }, React.createElement(MachineCard, {
    image: '../../assets/machine-mec-ld.png',
    name: 'MEC LD',
    subtitle: 'Gravity filling monobloc',
    stat1: {
      value: '24k',
      label: 'Bottles / hour'
    },
    stat2: {
      value: '±1 mm',
      label: 'Fill accuracy'
    },
    href: '#mecld'
  }), React.createElement(MachineCard, {
    image: '../../assets/machine-mec-pv.png',
    name: 'MEC PV',
    subtitle: 'Volumetric filling',
    stat1: {
      value: '12k',
      label: 'Bottles / hour'
    },
    stat2: {
      value: '±2 ml',
      label: 'Dosing accuracy'
    }
  }), React.createElement(MachineCard, {
    image: '../../assets/machine-powerfill.png',
    name: 'POWERFILL',
    subtitle: 'High-speed isobaric',
    stat1: {
      value: '36k',
      label: 'Bottles / hour'
    },
    stat2: {
      value: '6 bar',
      label: 'Isobaric pressure'
    }
  })))));
}
window.Home = Home;
})(); } catch (e) { __ds_ns.__errors.push({ path: "ui_kits/marketing-site/Home.jsx", error: String((e && e.message) || e) }); }

// ui_kits/marketing-site/MachinesFilling.jsx
try { (() => {
const {
  SectionKicker,
  MachineCard,
  Tag
} = window.EurostarDesignSystem_8ffca8;
const techs = [['01', 'Gravity', 'Level filling for still, low-density liquids — valves open on contact with the neck.'], ['02', 'Volumetric', 'Piston-dosed exact volumes, ideal for oils, syrups and dense products.'], ['03', 'Isobaric', 'Counter-pressure filling that keeps CO₂ in sparkling wines and soft drinks.'], ['04', 'Vacuum', 'Gentle draw-down for fragile containers and precise level control.']];
function MachinesFilling({
  goto
}) {
  return React.createElement('div', null, React.createElement('section', {
    style: {
      background: 'var(--surface-dark)',
      padding: '90px clamp(20px,6vw,80px) 70px',
      position: 'relative',
      overflow: 'hidden',
      minHeight: '50vh',
      display: 'flex',
      alignItems: 'center'
    }
  }, React.createElement('img', {
    src: '../../assets/hub-filling-hero.png',
    alt: '',
    style: {
      position: 'absolute',
      inset: 0,
      width: '100%',
      height: '100%',
      objectFit: 'cover',
      opacity: 0.5
    }
  }), React.createElement('div', {
    style: {
      position: 'absolute',
      inset: 0,
      background: 'linear-gradient(90deg,rgba(20,33,63,.92) 0%,rgba(20,33,63,.5) 70%)'
    }
  }), React.createElement('div', {
    style: {
      position: 'relative',
      maxWidth: 1200,
      margin: '0 auto'
    }
  }, React.createElement(SectionKicker, {
    dark: true
  }, 'Machines / Filling'), React.createElement('h1', {
    style: {
      margin: '18px 0 0',
      font: 'var(--text-display-1)',
      color: '#fff',
      textTransform: 'uppercase'
    }
  }, 'Bottle filling machines'), React.createElement('p', {
    style: {
      margin: '26px 0 0',
      font: 'var(--text-body-lg)',
      color: 'rgba(255,255,255,.85)',
      maxWidth: 620
    }
  }, 'Gravity, volumetric, isobaric and vacuum systems engineered for still and sparkling products — precise levels, gentle handling, oxidation kept to a minimum.'))), React.createElement('section', {
    style: {
      background: 'var(--surface-tint)',
      padding: 'var(--space-section-y) var(--space-section-x)'
    }
  }, React.createElement('div', {
    style: {
      maxWidth: 1360,
      margin: '0 auto'
    }
  }, React.createElement(SectionKicker, null, 'How we fill'), React.createElement('div', {
    style: {
      display: 'grid',
      gridTemplateColumns: 'repeat(auto-fit,minmax(200px,1fr))',
      gap: 32,
      marginTop: 32
    }
  }, techs.map(([n, t, d]) => React.createElement('div', {
    key: n
  }, React.createElement('div', {
    style: {
      fontFamily: 'var(--font-display)',
      fontWeight: 800,
      fontSize: 14,
      color: 'var(--blue-500)',
      paddingTop: 16,
      borderTop: '2px solid var(--text-heading)'
    }
  }, n), React.createElement('h3', {
    style: {
      margin: '12px 0 8px',
      font: 'var(--text-h3)',
      color: 'var(--text-heading)',
      textTransform: 'uppercase'
    }
  }, t), React.createElement('p', {
    style: {
      margin: 0,
      font: 'var(--text-body-md)',
      color: 'var(--text-body)'
    }
  }, d)))))), React.createElement('section', {
    style: {
      background: '#fff',
      padding: 'var(--space-section-y) var(--space-section-x)'
    }
  }, React.createElement('div', {
    style: {
      maxWidth: 1360,
      margin: '0 auto'
    }
  }, React.createElement('h2', {
    style: {
      margin: '0 0 32px',
      font: 'var(--text-display-2)',
      color: 'var(--text-heading)',
      textTransform: 'uppercase'
    }
  }, React.createElement('span', null, 'The filling '), React.createElement('span', {
    style: {
      color: 'var(--blue-500)'
    }
  }, 'range')), React.createElement('div', {
    style: {
      display: 'grid',
      gridTemplateColumns: 'repeat(auto-fit,minmax(240px,1fr))',
      gap: 24
    },
    onClick: e => {
      const a = e.target.closest('[data-mecld]');
      if (a) goto('mecld');
    }
  }, React.createElement('div', {
    'data-mecld': true,
    style: {
      cursor: 'pointer'
    }
  }, React.createElement(MachineCard, {
    image: '../../assets/machine-mec-ld.png',
    name: 'MEC LD',
    subtitle: 'Gravity filling monobloc',
    stat1: {
      value: '24k',
      label: 'Bottles / hour'
    },
    stat2: {
      value: '±1 mm',
      label: 'Fill accuracy'
    }
  })), React.createElement(MachineCard, {
    image: '../../assets/machine-mec-pv.png',
    name: 'MEC PV',
    subtitle: 'Volumetric filling',
    stat1: {
      value: '12k',
      label: 'Bottles / hour'
    },
    stat2: {
      value: '±2 ml',
      label: 'Dosing accuracy'
    }
  }), React.createElement(MachineCard, {
    image: '../../assets/machine-powerfill.png',
    name: 'POWERFILL',
    subtitle: 'High-speed isobaric',
    stat1: {
      value: '36k',
      label: 'Bottles / hour'
    },
    stat2: {
      value: '6 bar',
      label: 'Isobaric pressure'
    }
  }), React.createElement(MachineCard, {
    image: '../../assets/machine-canfill-9-1.png',
    name: 'CANFILL 9.1',
    subtitle: 'Can filling & seaming',
    stat1: {
      value: '30k',
      label: 'Cans / hour'
    },
    stat2: {
      value: '9',
      label: 'Filling valves'
    }
  })))));
}
window.MachinesFilling = MachinesFilling;
})(); } catch (e) { __ds_ns.__errors.push({ path: "ui_kits/marketing-site/MachinesFilling.jsx", error: String((e && e.message) || e) }); }

// ui_kits/marketing-site/MecLd.jsx
try { (() => {
const {
  SectionKicker,
  Button,
  SpecList,
  SpecRow,
  StatBlock,
  Tag
} = window.EurostarDesignSystem_8ffca8;
function MecLd() {
  return React.createElement('div', null, React.createElement('section', {
    style: {
      background: '#FEFEFC',
      padding: '90px clamp(20px,6vw,80px) 60px',
      minHeight: '70vh',
      display: 'flex',
      alignItems: 'center'
    }
  }, React.createElement('div', {
    style: {
      maxWidth: 1360,
      margin: '0 auto',
      display: 'grid',
      gridTemplateColumns: '1.1fr 0.9fr',
      gap: 40,
      alignItems: 'center'
    }
  }, React.createElement('div', null, React.createElement(SectionKicker, null, 'Gravity & low-vacuum filling'), React.createElement('h1', {
    style: {
      margin: '18px 0 0',
      font: 'var(--text-h1-product)',
      color: 'var(--text-heading)',
      textTransform: 'uppercase'
    }
  }, 'MEC LD'), React.createElement('p', {
    style: {
      margin: '24px 0 0',
      font: 'var(--text-body-lg)',
      color: 'var(--text-body)',
      maxWidth: 480
    }
  }, 'An automatic rotary monobloc for still, low-density liquids. The valve opens on contact with the bottle neck — gentle, precise level filling across a broad range of formats.'), React.createElement('div', {
    style: {
      display: 'flex',
      gap: 40,
      marginTop: 32,
      borderTop: '1px solid var(--border-subtle)',
      paddingTop: 18
    }
  }, React.createElement(StatBlock, {
    value: '1k–24k',
    label: 'Bottles / hour',
    dark: false,
    size: 'sm'
  }), React.createElement(StatBlock, {
    value: '2',
    label: 'Rotary stations',
    dark: false,
    size: 'sm'
  })), React.createElement('div', {
    style: {
      marginTop: 32,
      display: 'flex',
      gap: 14
    }
  }, React.createElement(Button, {
    variant: 'primary'
  }, 'Request a quote'), React.createElement(Button, {
    variant: 'text'
  }, 'Datasheet'))), React.createElement('div', {
    style: {
      background: 'var(--surface-card)',
      aspectRatio: '1/1',
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center'
    }
  }, React.createElement('img', {
    src: '../../assets/machine-mec-ld.png',
    alt: 'MEC LD',
    style: {
      width: '80%',
      height: '80%',
      objectFit: 'contain'
    }
  })))), React.createElement('section', {
    style: {
      background: '#0B1330',
      padding: 'var(--space-section-y-lg) var(--space-section-x)'
    }
  }, React.createElement('div', {
    style: {
      maxWidth: 1180,
      margin: '0 auto'
    }
  }, React.createElement(SectionKicker, {
    dark: true
  }, 'The principle'), React.createElement('h2', {
    style: {
      margin: '24px 0 0',
      font: 'var(--text-display-3)',
      color: '#fff',
      textTransform: 'uppercase'
    }
  }, 'Filling that never disturbs the ', React.createElement('span', {
    style: {
      color: 'var(--blue-300)'
    }
  }, 'liquid.')), React.createElement('p', {
    style: {
      margin: '28px 0 0',
      font: 'var(--text-body-lg)',
      color: 'rgba(255,255,255,.82)',
      maxWidth: 640
    }
  }, 'Air is drawn from the bottle, replaced with inert gas, and the exact level is set with excess returned to a separate tank. The product reaches the cap untouched — oxidation reduced to a minimum.'))), React.createElement('section', {
    style: {
      background: '#fff',
      padding: 'var(--space-section-y) var(--space-section-x)'
    }
  }, React.createElement('div', {
    style: {
      maxWidth: 900,
      margin: '0 auto'
    }
  }, React.createElement(SectionKicker, null, 'Technical datasheet'), React.createElement('div', {
    style: {
      marginTop: 24
    }
  }, React.createElement(SpecList, null, React.createElement(SpecRow, {
    label: 'Capacity'
  }, '1,000–24,000 bottles/hour'), React.createElement(SpecRow, {
    label: 'Structure'
  }, 'AISI 304 stainless steel; product-contact parts in AISI 316L'), React.createElement(SpecRow, {
    label: 'Control'
  }, '5.7" colour touch HMI, optional WiFi / Ethernet'), React.createElement(SpecRow, {
    label: 'Container types'
  }, 'PET, glass, wine bottle'))))));
}
window.MecLd = MecLd;
})(); } catch (e) { __ds_ns.__errors.push({ path: "ui_kits/marketing-site/MecLd.jsx", error: String((e && e.message) || e) }); }

// ui_kits/marketing-site/Nav.jsx
try { (() => {
function Nav({
  page,
  goto,
  light
}) {
  const linkStyle = {
    fontFamily: 'var(--font-display)',
    fontWeight: 700,
    fontSize: 13,
    letterSpacing: '0.14em',
    textTransform: 'uppercase',
    color: light ? 'var(--text-heading)' : '#fff',
    cursor: 'pointer',
    background: 'none',
    border: 'none',
    padding: 0
  };
  return React.createElement('nav', {
    style: {
      position: 'sticky',
      top: 0,
      zIndex: 50,
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'space-between',
      padding: '18px clamp(20px,4vw,56px)',
      background: light ? '#fff' : 'var(--surface-dark)',
      boxShadow: light ? 'var(--shadow-nav)' : 'none'
    }
  }, React.createElement('img', {
    src: '../../assets/' + (light ? 'logo-color-nav.png' : 'logo-white.png'),
    alt: 'Eurostar',
    style: {
      height: 40,
      cursor: 'pointer'
    },
    onClick: () => goto('home')
  }), React.createElement('div', {
    style: {
      display: 'flex',
      gap: 32,
      alignItems: 'center'
    }
  }, React.createElement('button', {
    style: {
      ...linkStyle,
      opacity: page === 'home' ? 1 : 0.75
    },
    onClick: () => goto('home')
  }, 'Company'), React.createElement('button', {
    style: {
      ...linkStyle,
      opacity: page === 'filling' || page === 'mecld' ? 1 : 0.75
    },
    onClick: () => goto('filling')
  }, 'Machines'), React.createElement('button', {
    style: linkStyle,
    onClick: () => goto('home')
  }, 'Industries'), React.createElement('button', {
    style: linkStyle,
    onClick: () => goto('home')
  }, 'References')));
}
window.Nav = Nav;
})(); } catch (e) { __ds_ns.__errors.push({ path: "ui_kits/marketing-site/Nav.jsx", error: String((e && e.message) || e) }); }

__ds_ns.Button = __ds_scope.Button;

__ds_ns.MachineCard = __ds_scope.MachineCard;

__ds_ns.SectionKicker = __ds_scope.SectionKicker;

__ds_ns.SpecRow = __ds_scope.SpecRow;

__ds_ns.SpecList = __ds_scope.SpecList;

__ds_ns.StatBlock = __ds_scope.StatBlock;

__ds_ns.Tag = __ds_scope.Tag;

})();
