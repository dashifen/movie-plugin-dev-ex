import {default as metadata} from './block.json';
import {registerBlockType} from '@wordpress/blocks';

registerBlockType(metadata, {
  edit: () => {
    return (
      "## Movie Block ##"
    );
  },

  save: () => null
});