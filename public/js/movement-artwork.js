/* Articulated original illustrations. Source files remain untouched.
 * Each region has its own mesh / joint motion; supports and contact points
 * remain in source coordinates. Canvas is visual only: the enclosing SVG
 * retains the player's accessible title and current movement description.
 */
(() => {
  'use strict';
  const clamp = (n, min = 0, max = 1) => Math.max(min, Math.min(max, n));
  const rotate = (x, y, cx, cy, angle) => {
    const c = Math.cos(angle), s = Math.sin(angle);
    return [cx + (x - cx) * c - (y - cy) * s, cy + (x - cx) * s + (y - cy) * c];
  };
  const polygon = (points) => {
    const path = new Path2D();
    points.forEach(([x, y], i) => i ? path.lineTo(x, y) : path.moveTo(x, y));
    path.closePath();
    return path;
  };

  // Region boundaries follow the clothing, neck and shoulder contours.
  // Overlapping interior pixels give joints coverage as their angle changes.
  const rigs = {
    'cat-neutral': {
      width: 1536, height: 1024, box: [48, 74, 408, 272],
      regions: [
        { points: [[0,780],[790,780],[790,570],[765,470],[745,360],[730,305],[730,0],[0,0]], static: true },
        { points: [[0,650],[1030,650],[1030,435],[1536,435],[1536,1024],[0,1024]], static: true },
        { points: [[730,305],[730,0],[1165,0],[1200,216],[1200,312],[1162,380],[1190,650],[790,650],[790,570],[765,470],[745,360]], focus: 'M775 319C849 294 902 246 980 227Q1004 219 1021 230L1012 258C925 272 869 330 796 358Q782 346 775 319Z', move: (x,y,a) => {
          const weight = Math.sin(Math.PI * clamp((x-745)/300));
          return [x, y + (1-a)*60*weight*(1-.45*clamp((y-300)/330))];
        } },
        { points: [[1160,0],[1536,0],[1536,440],[1195,440],[1195,325],[1160,277]], rigid: { pivot: [1190,270], angle: a => -.12*(1-a) } },
        { points: [[1030,315],[1047,244],[1085,211],[1134,220],[1172,260],[1190,350],[1240,780],[1390,810],[1390,885],[1120,885],[1060,600]], static: true },
      ],
    },
    'calf-stretch': {
      width: 1536, height: 1024, box: [-38, 0, 555, 370],
      regions: [
        { points: [[1065,0],[1536,0],[1536,1024],[0,1024],[0,905],[820,905],[820,870],[1065,870]], static: true },
        { points: [[660,443],[796,473],[855,580],[923,685],[918,755],[901,878],[841,885],[819,741],[737,674],[661,570]], move: (x,y,a) => [x-55*(1-a)*clamp((875-y)/380),y] },
        { points: [[650,437],[830,493],[791,579],[705,699],[633,783],[575,913],[517,907],[546,785],[589,688],[613,574]], focus: 'M598 742Q628 731 626 759Q613 804 579 876Q567 885 550 872Q561 815 588 761Z', move: (x,y,a) => [x-55*(1-a)*clamp((905-y)/450),y] },
        { points: [[680,201],[745,198],[815,231],[848,307],[831,337],[868,393],[897,453],[835,515],[650,445],[704,362],[676,291]], move: (x,y,a) => [x-55*(1-a),y] },
        { points: [[590,0],[860,0],[860,193],[817,232],[777,227],[749,206],[676,249],[590,249]], move: (x,y,a) => [x-55*(1-a),y] },
        { points: [[715,210],[747,205],[811,234],[866,258],[913,272],[1038,269],[1051,247],[1063,168],[1088,155],[1093,290],[1064,304],[945,324],[870,323],[779,304],[713,292],[695,261]], columns: [1065], move: (x,y,a) => {
          const weight=clamp((1065-x)/345);
          return [x-55*(1-a)*weight,y+15*(1-a)*Math.sin(Math.PI*weight)];
        } },
      ],
    },
    'side-reach': {
      width: 1024, height: 1536, box: [120, 0, 260, 390],
      image: 'movement-side-reach-rig.webp',
      regions: [
        { points: [[280,760],[1024,760],[1024,1536],[0,1536],[0,760]], static: true },
        { points: [[280,100],[1024,100],[1024,960],[725,960],[670,780],[280,780]], rows: [780], focus: 'M420 494C466 535 441 578 425 621C409 659 402 695 414 728Q394 748 374 738C358 700 368 655 386 609C405 560 393 530 420 494Z', move: (x,y,a) => {
          const support=clamp((x-660)/100)*clamp((y-440)/200);
          const weight=clamp((780-y)/380)*(1-support);
          const head=clamp((420-y)/170);
          const [px,py]=rotate(x,y,600,420,-.16*(1-a)*head);
          return [px-95*(1-a)*weight,py-20*(1-a)*weight];
        } },
        { points: [[80,60],[270,60],[270,740],[80,740]], move: (x,y,a) => {
          // Complete, separately painted arm: shoulder rotation plus elbow flex.
          const elbowWeight=clamp((y-350)/100);
          const [ex,ey]=rotate(x,y,174,385,-.18*a*elbowWeight);
          const [px,py]=rotate(ex,ey,204,145,3.55*a);
          return [440+(px-204)*.76-95*(1-a),425+(py-145)*.76-20*(1-a)];
        } },
      ],
    },
  };

  function createRegion(image, region, width, height) {
    const xs=region.points.map(p=>p[0]), ys=region.points.map(p=>p[1]);
    const left=Math.max(0,Math.floor(Math.min(...xs))), top=Math.max(0,Math.floor(Math.min(...ys)));
    const right=Math.min(width,Math.ceil(Math.max(...xs))), bottom=Math.min(height,Math.ceil(Math.max(...ys)));
    const texture=document.createElement('canvas');
    texture.width=right-left; texture.height=bottom-top;
    const ctx=texture.getContext('2d');
    ctx.translate(-left,-top); ctx.clip(polygon(region.points)); ctx.drawImage(image,0,0);
    if (region.focus) {
      // Paint on this region's texture before deformation: the focus follows
      // the same joints and occlusion as the clothing, never the background.
      ctx.globalCompositeOperation='source-atop';
      const focus=new Path2D(region.focus);
      ctx.fillStyle='rgba(206, 36, 47, .48)';
      ctx.fill(focus);
      ctx.strokeStyle='rgba(166, 35, 43, .7)';
      ctx.lineWidth=2.5;
      ctx.stroke(focus);
      ctx.globalCompositeOperation='source-over';
    }
    const triangles=[];
    // Put a mesh edge exactly on a pinned joint boundary, so interpolation
    // cannot drag fixed pixels along with the adjacent moving region.
    const grid=(start,end,anchors=[]) => {
      const values=[end,...anchors.filter(value=>value>start && value<end)];
      for(let value=start;value<end;value+=80) values.push(value);
      return [...new Set(values)].sort((a,b)=>a-b);
    };
    const columns=grid(left,right,region.columns), rows=grid(top,bottom,region.rows);
    if (!region.static && !region.rigid) for(let row=0;row<rows.length-1;row++) for(let col=0;col<columns.length-1;col++) {
      const x=columns[col], y=rows[row], r=columns[col+1], b=rows[row+1];
      triangles.push([[x,y],[r,y],[r,b]],[[x,y],[r,b],[x,b]]);
    }
    return {...region,texture,left,top,triangles};
  }

  function triangle(ctx, region, source, target) {
    const [p0,p1,p2]=source, [q0,q1,q2]=target;
    const u=p1[0]-p0[0], v=p1[1]-p0[1], w=p2[0]-p0[0], z=p2[1]-p0[1];
    const det=u*z-v*w;
    const a=((q1[0]-q0[0])*z-(q2[0]-q0[0])*v)/det;
    const c=((q2[0]-q0[0])*u-(q1[0]-q0[0])*w)/det;
    const b=((q1[1]-q0[1])*z-(q2[1]-q0[1])*v)/det;
    const d=((q2[1]-q0[1])*u-(q1[1]-q0[1])*w)/det;
    ctx.save();
    // A subpixel overlap removes antialias cracks between adjacent triangles.
    const mx=(q0[0]+q1[0]+q2[0])/3, my=(q0[1]+q1[1]+q2[1])/3;
    ctx.beginPath();
    target.forEach(([x,y],i)=>{
      const length=Math.hypot(x-mx,y-my)||1;
      const px=x+(x-mx)/length*.8, py=y+(y-my)/length*.8;
      i ? ctx.lineTo(px,py) : ctx.moveTo(px,py);
    });
    ctx.closePath(); ctx.clip();
    ctx.transform(a,b,c,d,q0[0]-a*p0[0]-c*p0[1],q0[1]-b*p0[0]-d*p0[1]);
    ctx.drawImage(region.texture,region.left,region.top);
    ctx.restore();
  }

  window.MovementArtwork = (host, choices, repaint) => {
    const ns='http://www.w3.org/2000/svg';
    const surface=document.createElementNS(ns,'foreignObject');
    surface.setAttribute('x','0'); surface.setAttribute('y','0');
    surface.setAttribute('width','500'); surface.setAttribute('height','390');
    surface.setAttribute('aria-hidden','true');
    const canvas=document.createElement('canvas');
    canvas.width=1000; canvas.height=780;
    canvas.style.cssText='display:block;width:500px;height:390px;pointer-events:none';
    surface.append(canvas);
    const ctx=canvas.getContext('2d');
    if (!ctx) return () => false;
    const ready=new Map();
    let lastKey, lastAmount;
    for(const [key,rig] of Object.entries(rigs)) {
      const source=choices.find(button=>button.dataset.msChoice===key).querySelector('img');
      const image=new Image();
      image.onload=()=>{
        ready.set(key,{...rig,regions:rig.regions.map(region=>createRegion(image,region,rig.width,rig.height))});
        repaint();
      };
      image.onerror=() => repaint();
      image.src=rig.image ? new URL(rig.image, source.src).href : source.src;
    }
    return (key,amount) => {
      const rig=ready.get(key);
      if(!rig) return false;
      if(surface.parentNode===host && key===lastKey && amount===lastAmount) return true;
      if(surface.parentNode!==host) host.replaceChildren(surface);
      lastKey=key; lastAmount=amount;
      ctx.setTransform(1,0,0,1,0,0); ctx.clearRect(0,0,canvas.width,canvas.height);
      const [x,y,w,h]=rig.box;
      ctx.setTransform(2*w/rig.width,0,0,2*h/rig.height,2*x,2*y);
      for(const region of rig.regions) {
        if (region.static) {
          ctx.drawImage(region.texture,region.left,region.top);
          continue;
        }
        if (region.rigid) {
          // Keep the face as one intact image: no triangle seams or jaw deformation.
          const [px,py]=region.rigid.pivot;
          ctx.save();
          ctx.translate(px,py); ctx.rotate(region.rigid.angle(amount)); ctx.translate(-px,-py);
          ctx.drawImage(region.texture,region.left,region.top);
          ctx.restore();
          continue;
        }
        for(const points of region.triangles) {
          triangle(ctx,region,points,points.map(([px,py])=>region.move(px,py,amount)));
        }
      }
      return true;
    };
  };
})();
